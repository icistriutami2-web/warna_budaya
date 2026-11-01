<?php

namespace Tests\Feature;

use App\Models\CulturalEntry;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminMediaSourceManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'session.driver' => 'array',
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createSchema();
    }

    public function test_admin_can_open_the_combined_media_and_sources_management_page(): void
    {
        $response = $this->actingAs($this->user(User::ROLE_ADMIN))
            ->get(route('admin.media-sources.page'));

        $response->assertOk()
            ->assertSee('Media dan')
            ->assertSee('Sumber Referensi')
            ->assertSee('admin/api/media')
            ->assertSee('admin/api/sources')
            ->assertSee('admin/api/cultural-entries/with-media')
            ->assertSee('entry[title]')
            ->assertSee('entry[slug]')
            ->assertSee('data-custom-category-select')
            ->assertSee('data-custom-entry-panel')
            ->assertSee('media-sources-tabs')
            ->assertSee('data-media-sources-config');
    }

    public function test_admin_can_load_media_and_source_lists_for_the_management_page(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->getJson(route('admin.api.media.index'))
            ->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links']);

        $this->getJson(route('admin.api.sources.index'))
            ->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links']);
    }

    public function test_unexpected_admin_api_errors_return_safe_json_without_exception_details(): void
    {
        Route::get('/admin/api/test-unexpected-error', function () {
            throw new \RuntimeException('Sensitive database path: D:\\private\\mysql\\data');
        });

        $this->getJson('/admin/api/test-unexpected-error')
            ->assertInternalServerError()
            ->assertExactJson([
                'message' => 'Terjadi kesalahan pada layanan. Silakan coba kembali.',
            ])
            ->assertDontSee('Sensitive database path')
            ->assertDontSee('RuntimeException');
    }

    public function test_only_admin_roles_can_access_media_and_source_endpoints(): void
    {
        $this->getJson(route('admin.api.media.index'))->assertUnauthorized();
        $this->getJson(route('admin.api.sources.index'))->assertUnauthorized();

        foreach ([User::ROLE_VERIFIKATOR, null] as $role) {
            $user = $this->user($role);
            $this->actingAs($user)->getJson(route('admin.api.media.index'))->assertForbidden();
            $this->actingAs($user)->getJson(route('admin.api.sources.index'))->assertForbidden();
        }

        foreach ([User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN] as $role) {
            $this->actingAs($this->user($role))
                ->getJson(route('admin.api.media.index'))
                ->assertOk();
            $this->getJson(route('admin.api.sources.index'))->assertOk();
        }
    }

    public function test_media_crud_validates_entry_and_preserves_permission_workflow_and_entry(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $entryId = $this->entry('Angklung');

        $this->actingAs($admin)
            ->postJson(route('admin.api.media.store'), [
                'cultural_entry_id' => 999,
                'is_cover' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cultural_entry_id', 'file_path', 'external_url']);

        $response = $this->postJson(route('admin.api.media.store'), [
            'cultural_entry_id' => $entryId,
            'media_type' => 'image',
            'file_path' => 'media/angklung.jpg',
            'caption' => 'Foto alat musik',
            'license' => 'CC BY 4.0',
            'permission_notes' => 'Izin awal tercatat.',
            'is_cover' => true,
            'sort_order' => 1,
        ])->assertCreated()
            ->assertJsonPath('data.permission_status', 'pending')
            ->assertJsonPath('data.cultural_entry.title', 'Angklung')
            ->assertJsonPath('data.file_path', 'media/angklung.jpg');

        $mediaId = $response->json('data.id');
        $this->putJson(route('admin.api.media.update', $mediaId), [
            'cultural_entry_id' => $entryId,
            'media_type' => 'image',
            'external_url' => 'https://example.test/angklung.jpg',
            'caption' => 'Foto terbaru',
            'is_cover' => false,
        ])->assertOk()
            ->assertJsonPath('data.permission_status', 'pending')
            ->assertJsonPath('data.external_url', 'https://example.test/angklung.jpg')
            ->assertJsonPath('data.permission_notes', 'Izin awal tercatat.');

        $this->deleteJson(route('admin.api.media.destroy', $mediaId))
            ->assertOk()
            ->assertJsonPath('message', 'Data media berhasil dihapus. Berkas fisik tidak dihapus.');

        $this->assertDatabaseMissing('media', ['id' => $mediaId]);
        $this->assertDatabaseHas('cultural_entries', ['id' => $entryId, 'title' => 'Angklung']);
    }

    public function test_media_cover_is_unique_per_entry_and_listing_searches_and_paginates(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $this->actingAs($admin);
        $entryId = $this->entry('Sasando');
        $this->postJson(route('admin.api.media.store'), [
            'cultural_entry_id' => $entryId,
            'media_type' => 'image',
            'file_path' => 'media/one.jpg',
            'caption' => 'Foto pertama',
            'is_cover' => true,
        ])->assertCreated();
        $this->postJson(route('admin.api.media.store'), [
            'cultural_entry_id' => $entryId,
            'media_type' => 'image',
            'file_path' => 'media/two.jpg',
            'caption' => 'Foto kedua',
            'is_cover' => true,
        ])->assertCreated();

        $this->assertSame(
            1,
            DB::table('media')->where('cultural_entry_id', $entryId)->where('is_cover', true)->count()
        );

        $this->getJson(route('admin.api.media.index', ['search' => 'Foto kedua', 'per_page' => 1]))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.caption', 'Foto kedua');

        $secondMediaId = DB::table('media')->where('caption', 'Foto kedua')->value('id');
        DB::table('media')->where('id', $secondMediaId)->update(['permission_status' => 'granted']);

        $this->getJson(route('admin.api.media.index', [
            'media_type' => 'image',
            'permission_status' => 'granted',
            'cultural_entry_id' => $entryId,
            'per_page' => 1,
        ]))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $secondMediaId)
            ->assertJsonPath('data.0.cultural_entry.title', 'Sasando');
    }

    public function test_source_crud_syncs_actual_pivot_and_protects_linked_sources(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $firstEntry = $this->entry('Tari Saman');
        $secondEntry = $this->entry('Tari Piring');

        $response = $this->actingAs($admin)->postJson(route('admin.api.sources.store'), [
            'source_type' => 'Buku',
            'title' => 'Ensiklopedia Tari',
            'author' => 'Penulis',
            'publication_year' => 2020,
            'cultural_entry_ids' => [$firstEntry],
        ])->assertCreated()
            ->assertJsonPath('data.cultural_entries.0.id', $firstEntry);

        $sourceId = $response->json('data.id');
        $this->assertDatabaseHas('cultural_entry_source', [
            'cultural_entry_id' => $firstEntry,
            'source_id' => $sourceId,
        ]);
        $this->assertFalse(Schema::hasColumn('cultural_entry_source', 'reference_note'));
        $this->assertSame(
            [$sourceId],
            CulturalEntry::query()->findOrFail($firstEntry)->sources()->pluck('sources.id')->all()
        );
        $this->assertSame(
            [$firstEntry],
            Source::query()->findOrFail($sourceId)->culturalEntries()->pluck('cultural_entries.id')->all()
        );

        $this->deleteJson(route('admin.api.sources.destroy', $sourceId))
            ->assertStatus(409);

        $this->putJson(route('admin.api.sources.update', $sourceId), [
            'source_type' => 'Buku',
            'title' => 'Ensiklopedia Tari Edisi Baru',
            'cultural_entry_ids' => [$secondEntry],
        ])->assertOk()
            ->assertJsonPath('data.cultural_entries.0.id', $secondEntry);

        $this->assertDatabaseMissing('cultural_entry_source', [
            'cultural_entry_id' => $firstEntry,
            'source_id' => $sourceId,
        ]);
        $this->assertDatabaseHas('cultural_entry_source', [
            'cultural_entry_id' => $secondEntry,
            'source_id' => $sourceId,
        ]);

        DB::table('cultural_entry_source')->where('source_id', $sourceId)->delete();
        $this->deleteJson(route('admin.api.sources.destroy', $sourceId))->assertOk();
        $this->assertDatabaseMissing('sources', ['id' => $sourceId]);
    }

    public function test_source_search_and_validation_reject_invalid_relationships(): void
    {
        $admin = $this->user(User::ROLE_ADMIN);
        $this->actingAs($admin)
            ->postJson(route('admin.api.sources.store'), [
                'title' => '',
                'publication_year' => 12000,
                'cultural_entry_ids' => [999],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'publication_year', 'cultural_entry_ids.0']);

        $this->postJson(route('admin.api.sources.store'), [
            'title' => 'Sumber Kesenian Daerah',
            'author' => 'Peneliti',
        ])->assertCreated();

        $this->getJson(route('admin.api.sources.index', ['search' => 'Kesenian', 'per_page' => 1]))
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Sumber Kesenian Daerah');
    }

    private function user(?string $role): User
    {
        return (new User)->forceFill([
            'id' => random_int(1, 100000),
            'name' => 'Test Admin',
            'email' => uniqid('admin-', true).'@example.test',
            'password' => 'hashed-password',
            'role' => $role,
        ]);
    }

    private function entry(string $title): int
    {
        return (int) DB::table('cultural_entries')->insertGetId([
            'title' => $title,
            'slug' => str_replace(' ', '-', strtolower($title)).'-'.uniqid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('role')->nullable();
            $table->timestamps();
        });

        Schema::create('cultural_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('cultural_entry_id');
            $table->string('media_type', 100)->nullable();
            $table->string('file_path')->nullable();
            $table->text('external_url')->nullable();
            $table->text('caption')->nullable();
            $table->text('alt_text')->nullable();
            $table->string('creator')->nullable();
            $table->string('copyright_holder')->nullable();
            $table->string('license')->nullable();
            $table->string('permission_status', 50)->nullable()->default('pending');
            $table->text('permission_notes')->nullable();
            $table->boolean('is_cover')->nullable()->default(false);
            $table->unsignedInteger('sort_order')->nullable()->default(0);
            $table->timestamps();
            $table->foreign('cultural_entry_id')
                ->references('id')
                ->on('cultural_entries')
                ->cascadeOnDelete();
        });

        Schema::create('sources', function (Blueprint $table): void {
            $table->id();
            $table->string('source_type', 100)->nullable();
            $table->string('title', 255);
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->unsignedSmallInteger('publication_year')->nullable();
            $table->text('url')->nullable();
            $table->string('identifier')->nullable();
            $table->timestamp('accessed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cultural_entry_source', function (Blueprint $table): void {
            $table->unsignedBigInteger('cultural_entry_id');
            $table->unsignedBigInteger('source_id');
            $table->primary(['cultural_entry_id', 'source_id']);
        });
    }
}
