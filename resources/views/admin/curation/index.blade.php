@extends('admin.layouts.app')

@section('title', 'Dashboard Kurasi | WARNA BUDAYA')

@section('content')
    <section class="admin-page-heading" id="dashboard-overview">
        <div>
            <p class="admin-eyebrow"><span></span> Pusat pengelolaan arsip</p>
            <h1>Dashboard <em>Kurasi.</em></h1>
            <p>Periksa informasi, verifikasi sumber, dan kelola publikasi budaya satu per satu.</p>
        </div>
        <div class="admin-heading-stamp" aria-hidden="true">WB<small>ARSIP<br>NUSANTARA</small></div>
    </section>

    <section class="admin-stat-grid" aria-label="Ringkasan kurasi">
        <article class="admin-stat-card admin-stat-featured">
            <span class="admin-stat-label">Seluruh entri</span>
            <strong>{{ number_format($stats['total'], 0, ',', '.') }}</strong>
            <small>Termasuk data draft dan pending</small>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Draft publikasi</span>
            <strong>{{ number_format($stats['draft'], 0, ',', '.') }}</strong>
            <small>Status publikasi draft</small>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Menunggu verifikasi</span>
            <strong>{{ number_format($stats['pending'], 0, ',', '.') }}</strong>
            <small>Status verifikasi pending</small>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Terverifikasi</span>
            <strong>{{ number_format($stats['verified'], 0, ',', '.') }}</strong>
            <small>Belum tentu sudah dipublikasikan</small>
        </article>
        <article class="admin-stat-card">
            <span class="admin-stat-label">Sudah dipublikasikan</span>
            <strong>{{ number_format($stats['published'], 0, ',', '.') }}</strong>
            <small>Entri dengan status published</small>
        </article>
        <article class="admin-stat-card admin-stat-media">
            <span class="admin-stat-label">Izin media pending</span>
            <strong>{{ number_format($stats['media_pending'], 0, ',', '.') }}</strong>
            <small>{{ number_format($stats['media_granted'], 0, ',', '.') }} media mendapat izin</small>
        </article>
        @if ($stats['deleted'] > 0)
            <article class="admin-stat-card admin-stat-deleted">
                <span class="admin-stat-label">Soft deleted</span>
                <strong>{{ number_format($stats['deleted'], 0, ',', '.') }}</strong>
                <small>Tetap tersimpan dan tidak tampil ke publik</small>
            </article>
        @endif
    </section>

    <section class="admin-workspace" id="data-budaya" aria-labelledby="entry-list-title">
        <div class="admin-section-heading">
            <div>
                <p class="admin-eyebrow"><span></span> Daftar kerja</p>
                <h2 id="entry-list-title">Entri budaya</h2>
            </div>
            <span class="admin-total-label">{{ number_format($entries->total(), 0, ',', '.') }} hasil</span>
        </div>

        <form class="admin-filters" action="{{ route('admin.home') }}" method="GET">
            <label class="admin-filter-search">
                <span class="admin-sr-only">Cari nama budaya</span>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama budaya...">
            </label>
            <label>
                <span class="admin-sr-only">Filter wilayah</span>
                <select name="region_id">
                    <option value="">Semua wilayah</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected((string) ($filters['region_id'] ?? '') === (string) $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="admin-sr-only">Filter kategori</span>
                <select name="category_id">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="admin-sr-only">Filter verifikasi</span>
                <select name="verification_status">
                    <option value="">Semua verifikasi</option>
                    <option value="pending" @selected(($filters['verification_status'] ?? '') === 'pending')>Pending</option>
                    <option value="verified" @selected(($filters['verification_status'] ?? '') === 'verified')>Verified</option>
                </select>
            </label>
            <label>
                <span class="admin-sr-only">Filter publikasi</span>
                <select name="publication_status">
                    <option value="">Semua publikasi</option>
                    <option value="draft" @selected(($filters['publication_status'] ?? '') === 'draft')>Draft</option>
                    <option value="published" @selected(($filters['publication_status'] ?? '') === 'published')>Published</option>
                </select>
            </label>
            <button type="submit">Terapkan</button>
            @if ($filters !== [])
                <a class="admin-filter-reset" href="{{ route('admin.home') }}">Reset</a>
            @endif
        </form>

        <div class="admin-table-wrap">
            <table class="admin-entry-table">
                <thead>
                    <tr>
                        <th scope="col">Nama budaya</th>
                        <th scope="col">Wilayah asal</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Verifikasi</th>
                        <th scope="col">Publikasi</th>
                        <th scope="col">Media</th>
                        <th scope="col">Dibuat</th>
                        <th scope="col"><span class="admin-sr-only">Tindakan</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        <tr @class(['is-deleted' => $entry->trashed()])>
                            <td data-label="Nama budaya">
                                <a class="admin-entry-name" href="{{ route('admin.curation.entries.show', $entry->id) }}">{{ $entry->title }}</a>
                                @if ($entry->trashed())
                                    <span class="admin-deleted-label">Soft deleted</span>
                                @endif
                            </td>
                            <td data-label="Wilayah">{{ $entry->region?->name ?? 'Belum ditentukan' }}</td>
                            <td data-label="Kategori">
                                @forelse ($entry->categories as $category)
                                    <span class="admin-category-tag">{{ $category->name }}</span>
                                @empty
                                    <span class="admin-muted">Belum dikategorikan</span>
                                @endforelse
                            </td>
                            <td data-label="Verifikasi">
                                <span @class(['admin-status', 'status-verified' => $entry->verification_status === 'verified', 'status-pending' => $entry->verification_status === 'pending', 'status-other' => ! in_array($entry->verification_status, ['verified', 'pending'], true)])>
                                    {{ $entry->verification_status }}
                                </span>
                            </td>
                            <td data-label="Publikasi">
                                <span @class(['admin-status', 'status-published' => $entry->publication_status === 'published', 'status-draft' => $entry->publication_status === 'draft', 'status-other' => ! in_array($entry->publication_status, ['published', 'draft'], true)])>
                                    {{ $entry->publication_status }}
                                </span>
                            </td>
                            <td data-label="Media">{{ number_format($entry->media_count, 0, ',', '.') }}</td>
                            <td data-label="Dibuat">{{ $entry->created_at?->format('d M Y') ?? '—' }}</td>
                            <td data-label="Tindakan"><a class="admin-row-action" href="{{ route('admin.curation.entries.show', $entry->id) }}">Periksa <span aria-hidden="true">↗</span></a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="admin-table-empty">Tidak ada entri yang cocok dengan filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($entries->hasPages())
            <nav class="admin-pagination" aria-label="Navigasi halaman entri">
                {{ $entries->links() }}
            </nav>
        @endif
    </section>
@endsection
