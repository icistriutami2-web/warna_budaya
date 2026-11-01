<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CulturalEntry;
use App\Models\Media;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CulturalCurationController extends Controller
{
    private const VERIFICATION_PENDING = 'pending';

    private const VERIFICATION_VERIFIED = 'verified';

    private const PUBLICATION_DRAFT = 'draft';

    private const PUBLICATION_PUBLISHED = 'published';

    private const PERMISSION_PENDING = 'pending';

    private const PERMISSION_GRANTED = 'granted';

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'verification_status' => ['nullable', 'in:pending,verified'],
            'publication_status' => ['nullable', 'in:draft,published'],
        ]);

        $allEntries = CulturalEntry::withTrashed();
        $stats = [
            'total' => (clone $allEntries)->count(),
            'draft' => (clone $allEntries)->where('publication_status', self::PUBLICATION_DRAFT)->count(),
            'pending' => (clone $allEntries)->where('verification_status', self::VERIFICATION_PENDING)->count(),
            'verified' => (clone $allEntries)->where('verification_status', self::VERIFICATION_VERIFIED)->count(),
            'published' => (clone $allEntries)->where('publication_status', self::PUBLICATION_PUBLISHED)->count(),
            'deleted' => (clone $allEntries)->whereNotNull('deleted_at')->count(),
            'media_pending' => Media::query()->where('permission_status', self::PERMISSION_PENDING)->count(),
            'media_granted' => Media::query()->where('permission_status', self::PERMISSION_GRANTED)->count(),
        ];

        $entries = CulturalEntry::withTrashed()
            ->with(['region', 'categories'])
            ->withCount('media')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('local_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['region_id'] ?? null, fn (Builder $query, int $regionId) => $query->where('origin_region_id', $regionId))
            ->when($filters['category_id'] ?? null, fn (Builder $query, int $categoryId) => $query->whereHas(
                'categories',
                fn (Builder $categoryQuery) => $categoryQuery->whereKey($categoryId)
            ))
            ->when($filters['verification_status'] ?? null, fn (Builder $query, string $status) => $query->where('verification_status', $status))
            ->when($filters['publication_status'] ?? null, fn (Builder $query, string $status) => $query->where('publication_status', $status))
            ->orderByDesc('updated_at')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString();

        return view('admin.curation.index', [
            'entries' => $entries,
            'stats' => $stats,
            'regions' => Region::query()->orderBy('name')->get(),
            'categories' => Category::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(int $entry): View
    {
        $culturalEntry = CulturalEntry::withTrashed()
            ->with([
                'region',
                'categories',
                'media' => fn (HasMany $query) => $query->orderByDesc('is_cover')->orderBy('sort_order')->orderBy('id'),
                'sources',
                'createdBy',
                'updatedBy',
                'verificationLogs.reviewer',
            ])
            ->findOrFail($entry);

        $culturalEntry->media->each(function (Media $media): void {
            $media->setAttribute(
                'preview_url',
                $media->file_path ? asset($media->file_path) : $this->safeExternalUrl($media->external_url)
            );
        });
        $culturalEntry->sources->each(fn ($source) => $source->setAttribute('safe_url', $this->safeExternalUrl($source->url)));

        return view('admin.curation.show', [
            'entry' => $culturalEntry,
            'publicationBlockers' => $this->publicationBlockers($culturalEntry),
        ]);
    }

    public function verify(Request $request, int $entry): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'notes' => ['required_if:decision,reject', 'nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($data, $entry): void {
            $culturalEntry = CulturalEntry::withTrashed()->lockForUpdate()->findOrFail($entry);
            abort_if($culturalEntry->trashed(), 404);

            $previousStatus = $culturalEntry->verification_status;
            $nextStatus = $data['decision'] === 'approve'
                ? self::VERIFICATION_VERIFIED
                : self::VERIFICATION_PENDING;
            $reviewedAt = now();

            $culturalEntry->forceFill([
                'verification_status' => $nextStatus,
                'verified_at' => $data['decision'] === 'approve' ? $reviewedAt : null,
                'updated_by' => $this->reviewerId(),
            ])->save();

            $this->recordHistory(
                $culturalEntry,
                $data['decision'] === 'approve' ? 'verified' : 'rejected',
                $reviewedAt,
                sprintf(
                    'Verifikasi: %s → %s.%s',
                    $previousStatus,
                    $nextStatus,
                    $this->noteSuffix($data['notes'] ?? null)
                )
            );
        });

        return redirect()
            ->route('admin.curation.entries.show', $entry)
            ->with('status', $data['decision'] === 'approve'
                ? 'Entri ditandai terverifikasi. Status publikasi tidak berubah.'
                : 'Penolakan verifikasi dan alasannya telah dicatat. Entri tetap menunggu perbaikan.');
    }

    public function reviewMedia(Request $request, int $entry, int $media): RedirectResponse
    {
        $data = $request->validate([
            'decision' => ['required', 'in:grant,reject,pending'],
            'notes' => ['required_if:decision,grant,reject', 'nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($data, $entry, $media): void {
            $culturalEntry = CulturalEntry::withTrashed()->lockForUpdate()->findOrFail($entry);
            abort_if($culturalEntry->trashed(), 404);

            $mediaItem = Media::query()
                ->where('cultural_entry_id', $culturalEntry->id)
                ->lockForUpdate()
                ->findOrFail($media);

            $previousStatus = $mediaItem->permission_status;
            $nextStatus = $data['decision'] === 'grant'
                ? self::PERMISSION_GRANTED
                : self::PERMISSION_PENDING;
            $reviewedAt = now();
            $note = trim((string) ($data['notes'] ?? ''));

            $mediaItem->forceFill([
                'permission_status' => $nextStatus,
                'permission_notes' => $this->appendMediaNote($mediaItem->permission_notes, $note),
            ])->save();

            $decision = match ($data['decision']) {
                'grant' => 'media_granted',
                'reject' => 'media_rejected',
                default => 'media_pending',
            };

            $this->recordHistory(
                $culturalEntry,
                $decision,
                $reviewedAt,
                sprintf(
                    'Media #%d, izin: %s → %s.%s',
                    $mediaItem->id,
                    $previousStatus,
                    $nextStatus,
                    $this->noteSuffix($note)
                )
            );
        });

        return redirect()
            ->route('admin.curation.entries.show', $entry)
            ->with('status', match ($data['decision']) {
                'grant' => 'Izin media disetujui dan dicatat.',
                'reject' => 'Penolakan izin media dicatat; status izin tetap pending agar media tidak tampil ke publik.',
                default => 'Media dikembalikan ke status pending.',
            });
    }

    public function publish(Request $request, int $entry): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:publish,draft'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $culturalEntry = CulturalEntry::withTrashed()->findOrFail($entry);
        $blockers = $data['action'] === 'publish' ? $this->publicationBlockers($culturalEntry) : [];

        if ($blockers !== []) {
            return back()
                ->withInput()
                ->with('publication_blockers', $blockers)
                ->withErrors(['publication' => 'Entri belum dapat dipublikasikan. Periksa syarat yang tercantum.']);
        }

        DB::transaction(function () use ($data, $entry): void {
            $culturalEntry = CulturalEntry::withTrashed()->lockForUpdate()->findOrFail($entry);
            abort_if($culturalEntry->trashed(), 404);

            if ($data['action'] === 'publish') {
                $currentBlockers = $this->publicationBlockers($culturalEntry);
                if ($currentBlockers !== []) {
                    throw ValidationException::withMessages([
                        'publication' => 'Entri belum dapat dipublikasikan: '.implode(' ', $currentBlockers),
                    ]);
                }
            }

            $previousStatus = $culturalEntry->publication_status;
            $nextStatus = $data['action'] === 'publish'
                ? self::PUBLICATION_PUBLISHED
                : self::PUBLICATION_DRAFT;
            $changedAt = now();

            if ($previousStatus === $nextStatus) {
                return;
            }

            $culturalEntry->forceFill([
                'publication_status' => $nextStatus,
                'published_at' => $nextStatus === self::PUBLICATION_PUBLISHED ? $changedAt : null,
                'updated_by' => $this->reviewerId(),
            ])->save();

            $decision = $nextStatus === self::PUBLICATION_PUBLISHED ? 'published' : 'withdrawn';
            $this->recordHistory(
                $culturalEntry,
                $decision,
                $changedAt,
                sprintf(
                    'Publikasi: %s → %s.%s',
                    $previousStatus,
                    $nextStatus,
                    $this->noteSuffix($data['notes'] ?? null)
                )
            );
        });

        return redirect()
            ->route('admin.curation.entries.show', $entry)
            ->with('status', $data['action'] === 'publish'
                ? 'Entri berhasil dipublikasikan.'
                : 'Entri ditarik dari publikasi dan disimpan sebagai draft.');
    }

    private function publicationBlockers(CulturalEntry $entry): array
    {
        $blockers = [];

        if ($entry->trashed()) {
            $blockers[] = 'Entri telah dihapus secara lunak dan tidak dapat dipublikasikan.';
        }

        if ($entry->verification_status !== self::VERIFICATION_VERIFIED) {
            $blockers[] = 'Entri harus berstatus verified.';
        }

        if (trim((string) $entry->title) === '') {
            $blockers[] = 'Nama budaya wajib tersedia.';
        }

        if (trim((string) ($entry->description ?: $entry->short_description)) === '') {
            $blockers[] = 'Deskripsi budaya wajib tersedia.';
        }

        if ($entry->origin_region_id === null || ! Region::query()->whereKey($entry->origin_region_id)->exists()) {
            $blockers[] = 'Wilayah asal yang valid wajib dipilih.';
        }

        return $blockers;
    }

    private function recordHistory(
        CulturalEntry $entry,
        string $decision,
        Carbon $reviewedAt,
        string $notes
    ): void {
        $entry->verificationLogs()->create([
            'reviewer_id' => $this->reviewerId(),
            'decision' => $decision,
            'notes' => trim($notes),
            'reviewed_at' => $reviewedAt,
        ]);
    }

    private function reviewerId(): int
    {
        return (int) auth()->id();
    }

    private function noteSuffix(?string $note): string
    {
        $note = trim((string) $note);

        return $note === '' ? '' : "\nCatatan admin: ".$note;
    }

    private function appendMediaNote(?string $existingNotes, string $note): ?string
    {
        if ($note === '') {
            return $existingNotes;
        }

        $timestampedNote = '['.now()->toDateTimeString().'] '.$note;

        return trim(implode("\n\n", array_filter([$existingNotes, $timestampedNote])));
    }

    private function safeExternalUrl(?string $url): ?string
    {
        if (! is_string($url) || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)
            ? $url
            : null;
    }
}
