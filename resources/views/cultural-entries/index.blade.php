@extends('layouts.app')

@section('title', 'Arsip Budaya Nusantara | WARNA BUDAYA')
@section('meta_description', 'Jelajahi arsip budaya terverifikasi dari berbagai wilayah Indonesia.')
@section('body_class', 'archive-page')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/archive.css') }}">
@endpush

@section('content')
    <section class="archive-hero" aria-labelledby="archive-title">
        <div class="page-shell archive-hero-inner">
            <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> Galeri digital warisan budaya</p>
            <h1 id="archive-title">Arsip Budaya <em>Nusantara.</em></h1>
            <p class="archive-hero-copy">Sebuah ruang untuk mengenali tradisi, pengetahuan, dan karya yang menjaga cerita Indonesia tetap hidup.</p>
            <div class="archive-hero-meta">
                <span class="archive-hero-mark" aria-hidden="true">✳</span>
                <span>{{ number_format($entries->total(), 0, ',', '.') }} arsip terkurasi</span>
                <span class="archive-meta-divider" aria-hidden="true"></span>
                <span>Warisan dari berbagai penjuru Indonesia</span>
            </div>
        </div>
        <span class="archive-hero-seal" aria-hidden="true">WB<small>ARSIP<br>BUDAYA</small></span>
    </section>

    <section class="archive-catalog section-space" aria-labelledby="archive-catalog-title">
        <div class="page-shell">
            <div class="archive-heading">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> Koleksi terkurasi</p>
                    <h2 id="archive-catalog-title" class="section-title">Temukan kisah <em>di balik warisan.</em></h2>
                </div>
                <p class="archive-heading-note">Jelajahi arsip budaya yang telah melalui proses verifikasi.</p>
            </div>

            <div class="archive-tools">
                <form class="archive-search" action="{{ route('cultural-entries.index') }}" method="GET" role="search">
                    <label class="sr-only" for="archive-search-input">Cari arsip budaya</label>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 4.5 4.5"></path></svg>
                    <input
                        id="archive-search-input"
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari tarian, kain, tradisi, dan lainnya"
                    >
                    <button type="submit">Cari arsip</button>
                </form>
                <p class="archive-result-count">
                    @if (request()->filled('search'))
                        {{ number_format($entries->total(), 0, ',', '.') }} hasil untuk “{{ request('search') }}”
                        <a href="{{ route('cultural-entries.index') }}">Hapus pencarian</a>
                    @else
                        Menampilkan {{ number_format($entries->total(), 0, ',', '.') }} arsip budaya
                    @endif
                </p>
            </div>

            @if ($entries->isNotEmpty())
                <div class="archive-grid">
                    @foreach ($entries as $entry)
                        @php
                            $archiveCover = $entry->media->first(
                                fn ($media) => $media->media_type === 'image' && ($media->external_url || $media->file_path)
                            );
                            $archiveCoverUrl = $archiveCover
                                ? ($archiveCover->external_url ?: asset($archiveCover->file_path))
                                : null;
                        @endphp

                        <article class="archive-card">
                            <a class="archive-card-visual" href="{{ route('cultural-entries.index', ['search' => $entry->title]) }}" aria-label="Lihat arsip {{ $entry->title }}">
                                @if ($archiveCoverUrl)
                                    <img
                                        src="{{ $archiveCoverUrl }}"
                                        alt="{{ $archiveCover->alt_text ?: $entry->title }}"
                                        loading="lazy"
                                    >
                                    <span class="archive-image-credit">{{ $archiveCover->caption ?: 'Dokumentasi arsip budaya' }}</span>
                                @else
                                    <span class="archive-art-fallback" aria-hidden="true">
                                        <span class="archive-fallback-mark">WB</span>
                                        <span>Arsip budaya Nusantara</span>
                                    </span>
                                @endif
                                <span class="archive-card-index" aria-hidden="true">{{ str_pad((string) $loop->iteration + (($entries->currentPage() - 1) * $entries->perPage()), 2, '0', STR_PAD_LEFT) }}</span>
                            </a>

                            <div class="archive-card-content">
                                <p class="archive-card-region">
                                    <span aria-hidden="true">✳</span>
                                    {{ $entry->region?->name ?? 'Wilayah belum tersedia' }}
                                </p>
                                <h3>
                                    <a href="{{ route('cultural-entries.index', ['search' => $entry->title]) }}">{{ $entry->title }}</a>
                                </h3>
                                @if ($entry->short_description)
                                    <p class="archive-card-description">{{ $entry->short_description }}</p>
                                @endif

                                @if ($entry->categories->isNotEmpty())
                                    <ul class="archive-card-categories" aria-label="Kategori">
                                        @foreach ($entry->categories as $category)
                                            <li>{{ $category->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                <a class="archive-card-link" href="{{ route('cultural-entries.index', ['search' => $entry->title]) }}">
                                    Jelajahi arsip <span aria-hidden="true">↗</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($entries->hasPages())
                    <nav class="archive-pagination" aria-label="Navigasi halaman arsip">
                        {{ $entries->links() }}
                    </nav>
                @endif
            @else
                <div class="archive-empty" role="status">
                    <span class="archive-empty-mark" aria-hidden="true">✳</span>
                    <h3>Belum ada kisah yang cocok.</h3>
                    <p>Coba gunakan kata kunci lain untuk menjelajahi arsip budaya Nusantara.</p>
                    @if (request()->filled('search'))
                        <a href="{{ route('cultural-entries.index') }}">Lihat semua arsip</a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection
