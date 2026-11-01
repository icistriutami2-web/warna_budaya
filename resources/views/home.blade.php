@extends('layouts.app')

@section('title', 'WARNA BUDAYA — Peta dan Arsip Digital Warisan Budaya Indonesia')
@section('meta_description', 'Temukan keindahan, cerita, dan warisan budaya dari seluruh penjuru Indonesia.')
@section('body_class', 'home-page')

@section('content')
    <section class="hero" aria-labelledby="hero-title">
        <div class="hero-art" aria-hidden="true">
            <img class="hero-backdrop" src="{{ asset('images/hero/wayang-kulit.jpg') }}" alt="" fetchpriority="high">
        </div>
        <div class="page-shell hero-content">
            <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> Peta dan arsip digital warisan budaya Indonesia</p>
            <h1 id="hero-title" aria-label="Setiap Daerah Punya Cerita.">Setiap Daerah<br><em>Punya Cerita.</em></h1>
            <p class="hero-description">Temukan keindahan, cerita, dan warisan budaya dari seluruh penjuru Indonesia.</p>
            <form class="hero-search" action="{{ route('cultural-entries.index') }}" method="GET" role="search">
                <label class="sr-only" for="hero-search">Cari arsip budaya</label>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 4.5 4.5"></path></svg>
                <input id="hero-search" type="search" name="search" placeholder="Cari tarian, kain, tradisi, dan lainnya">
                <button type="submit">Temukan</button>
            </form>
            <a class="button button-outline-light" href="#jelajah">
                Jelajahi Peta Budaya
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"></path></svg>
            </a>
        </div>
        <a class="hero-credit" href="https://commons.wikimedia.org/wiki/File:Bercerita_dengan_Wayang.jpg" target="_blank" rel="noopener noreferrer">Foto: Candra Firmansyah · CC BY-SA 4.0</a>
    </section>

    <section class="heritage-spotlight page-shell" aria-label="Jelajahi ragam budaya Nusantara">
        <article class="heritage-card">
            <div class="heritage-card-image">
                <a class="heritage-card-image-link" href="{{ route('cultural-entries.index', ['search' => 'batik']) }}" aria-label="Jelajahi arsip batik Nusantara">
                    <img src="{{ asset('images/hero/batik-tulis.jpg') }}" alt="Perajin membatik tulis di Imogiri, Yogyakarta" fetchpriority="high">
                </a>
                <a class="heritage-photo-credit" href="https://commons.wikimedia.org/wiki/File:Maestro_Batik_Tulis_di_Imogiri.jpg" target="_blank" rel="noopener noreferrer">Aconkyeah · CC BY-SA 4.0</a>
            </div>
            <a class="heritage-card-copy" href="{{ route('cultural-entries.index', ['search' => 'batik']) }}">
                <span class="heritage-card-kicker">01 · Seni & kriya</span>
                <span class="heritage-card-title">Batik Nusantara</span>
                <span class="heritage-card-description">Jejak tangan, warna, dan kisah dalam selembar kain.</span>
                <span class="heritage-card-arrow" aria-hidden="true">↗</span>
            </a>
        </article>
        <article class="heritage-card">
            <div class="heritage-card-image">
                <a class="heritage-card-image-link" href="{{ route('cultural-entries.index', ['search' => 'rumah adat']) }}" aria-label="Jelajahi arsip rumah adat Nusantara">
                    <img src="{{ asset('images/hero/tongkonan.jpg') }}" alt="Rumah adat Tongkonan di Toraja, Sulawesi Selatan" fetchpriority="high">
                </a>
                <a class="heritage-photo-credit" href="https://commons.wikimedia.org/wiki/File:Tongkonan,_Toraja_houses.jpg" target="_blank" rel="noopener noreferrer">RianHS · CC BY-SA 4.0</a>
            </div>
            <a class="heritage-card-copy" href="{{ route('cultural-entries.index', ['search' => 'rumah adat']) }}">
                <span class="heritage-card-kicker">02 · Arsitektur</span>
                <span class="heritage-card-title">Rumah Adat</span>
                <span class="heritage-card-description">Ruang hidup yang menyimpan nilai dan kebersamaan.</span>
                <span class="heritage-card-arrow" aria-hidden="true">↗</span>
            </a>
        </article>
        <article class="heritage-card">
            <div class="heritage-card-image">
                <a class="heritage-card-image-link" href="{{ route('cultural-entries.index', ['search' => 'tari']) }}" aria-label="Jelajahi arsip tari tradisional">
                    <img src="{{ asset('images/hero/pendet-dance.jpg') }}" alt="Penari Pendet Bali dalam busana tradisional" fetchpriority="high">
                </a>
                <a class="heritage-photo-credit" href="https://commons.wikimedia.org/wiki/File:Pendet_Dance.jpg" target="_blank" rel="noopener noreferrer">Hendri Suhandi · CC BY-SA 4.0</a>
            </div>
            <a class="heritage-card-copy" href="{{ route('cultural-entries.index', ['search' => 'tari']) }}">
                <span class="heritage-card-kicker">03 · Seni pertunjukan</span>
                <span class="heritage-card-title">Tari Tradisional</span>
                <span class="heritage-card-description">Gerak yang merawat ingatan dan makna lintas generasi.</span>
                <span class="heritage-card-arrow" aria-hidden="true">↗</span>
            </a>
        </article>
        <article class="heritage-card">
            <div class="heritage-card-image">
                <a class="heritage-card-image-link" href="{{ route('cultural-entries.index', ['search' => 'hidangan']) }}" aria-label="Jelajahi arsip kuliner daerah">
                    <img src="{{ asset('images/hero/nasi-tumpeng.jpg') }}" alt="Nasi tumpeng, hidangan tradisional dalam perayaan Indonesia" loading="eager">
                </a>
                <a class="heritage-photo-credit" href="https://commons.wikimedia.org/wiki/File:Nasi_Tumpeng_17an.jpg" target="_blank" rel="noopener noreferrer">Robih Ardiatama · CC BY-SA 4.0</a>
            </div>
            <a class="heritage-card-copy" href="{{ route('cultural-entries.index', ['search' => 'hidangan']) }}">
                <span class="heritage-card-kicker">04 · Tradisi & rasa</span>
                <span class="heritage-card-title">Kuliner Daerah</span>
                <span class="heritage-card-description">Resep, perayaan, dan cerita yang tersaji di meja.</span>
                <span class="heritage-card-arrow" aria-hidden="true">↗</span>
            </a>
        </article>
        <article class="heritage-card">
            <div class="heritage-card-image">
                <a class="heritage-card-image-link" href="{{ route('cultural-entries.index', ['search' => 'cerita']) }}" aria-label="Jelajahi arsip bahasa dan sastra Nusantara">
                    <img src="{{ asset('images/hero/naskah-lontar.jpg') }}" alt="Naskah lontar dan aksara Sunda sebagai warisan sastra" loading="eager">
                </a>
                <a class="heritage-photo-credit" href="https://commons.wikimedia.org/wiki/File:Kujang_dan_naskah_lontar_.jpg" target="_blank" rel="noopener noreferrer">Hd prastya · CC BY-SA 4.0</a>
            </div>
            <a class="heritage-card-copy" href="{{ route('cultural-entries.index', ['search' => 'cerita']) }}">
                <span class="heritage-card-kicker">05 · Bahasa & sastra</span>
                <span class="heritage-card-title">Bahasa & Sastra</span>
                <span class="heritage-card-description">Kata-kata dan naskah yang menjaga pengetahuan.</span>
                <span class="heritage-card-arrow" aria-hidden="true">↗</span>
            </a>
        </article>
    </section>

    <section class="intro-section section-space" id="jelajah" aria-labelledby="intro-title">
        <div class="page-shell intro-grid">
            <div class="intro-copy">
                <p class="eyebrow"><span class="eyebrow-line"></span> Sebuah perjalanan mengenal</p>
                <h2 class="section-title" id="intro-title">Indonesia hidup dalam <em>ragam cerita.</em></h2>
            </div>
            <div class="intro-aside">
                <p>Dari pengetahuan yang diwariskan hingga karya yang terus berkembang, setiap budaya menyimpan cara pandang tentang kehidupan dan tanah tempatnya tumbuh.</p>
                <a class="text-link" href="{{ route('cultural-entries.index') }}">Mulai menjelajah <span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <div class="page-shell">
            <div class="stats-band" aria-label="Sekilas tentang koleksi WARNA BUDAYA">
                <div class="stat-item"><strong>{{ number_format($provinceCount, 0, ',', '.') }}</strong><span>provinsi</span></div>
                <div class="stat-item"><strong>{{ number_format($categories->count(), 0, ',', '.') }}</strong><span>kategori budaya</span></div>
                <div class="stat-item"><strong>{{ number_format($entryCount, 0, ',', '.') }}</strong><span>entri dalam kurasi</span></div>
                <p>Setiap arsip dipersiapkan dengan perhatian pada konteks, sumber, dan hak penggunaan.</p>
            </div>
        </div>
    </section>

    <section class="category-section section-space" id="kategori" aria-labelledby="category-title">
        <div class="page-shell">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> Ragam warisan</p>
                    <h2 class="section-title" id="category-title">Jendela menuju <em>budaya kita.</em></h2>
                </div>
                <a class="text-link section-heading-link" href="{{ route('cultural-entries.index') }}">Lihat semua arsip <span aria-hidden="true">↗</span></a>
            </div>

            <div class="category-grid">
                @forelse ($categories as $category)
                        @php
                            $visual = $category->visual;
                        @endphp
                        <article class="category-card category-card-{{ ($loop->index % 5) + 1 }}">
                            @if ($visual)
                                <div class="category-art">
                                    <a class="category-art-link" href="{{ route('cultural-entries.index') }}" aria-label="Jelajahi arsip {{ $category->name }}">
                                        <img src="{{ asset($visual['image']) }}" alt="{{ $visual['alt'] }}" loading="lazy" decoding="async">
                                    </a>
                                    <span class="category-art-shade" aria-hidden="true"></span>
                                    <span class="category-art-mark" aria-hidden="true">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <a class="category-art-credit" href="{{ $visual['source'] }}" target="_blank" rel="noopener noreferrer" aria-label="Sumber foto {{ $visual['place'] }}: {{ $visual['credit'] }}">Foto: {{ $visual['credit'] }}</a>
                                </div>
                            @else
                                <div class="category-art category-art-fallback" aria-hidden="true">
                                    <span class="category-art-mark">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            @endif
                            <div class="category-card-copy">
                                <span class="category-card-count">{{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="category-card-title">{{ $category->name }}</h3>
                                <a class="category-card-link" href="{{ route('cultural-entries.index') }}">Jelajahi kategori <span aria-hidden="true">↗</span></a>
                            </div>
                        </article>
                @empty
                    <p class="empty-note">Kategori budaya sedang disiapkan.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="featured-section section-space" id="arsip" aria-labelledby="featured-title">
        <div class="page-shell">
            <div class="section-heading">
                <div>
                    <p class="eyebrow"><span class="eyebrow-line"></span> Dari arsip</p>
                    <h2 class="section-title" id="featured-title">Koleksi budaya <em>pilihan.</em></h2>
                </div>
                <a class="text-link section-heading-link" href="{{ route('cultural-entries.index') }}">Jelajahi arsip <span aria-hidden="true">↗</span></a>
            </div>

            @if ($featuredEntries->isNotEmpty())
                <div class="featured-grid">
                    @foreach ($featuredEntries as $entry)
                        @php
                            $cover = $entry->media->first();
                            $coverUrl = $cover?->external_url && \Illuminate\Support\Str::startsWith($cover->external_url, ['https://', 'http://'])
                                ? $cover->external_url
                                : ($cover?->file_path ? \Illuminate\Support\Facades\Storage::url($cover->file_path) : null);
                        @endphp
                        <article class="featured-card">
                            <a class="featured-image{{ $coverUrl ? ' has-image' : '' }}" href="{{ route('cultural-entries.index', ['search' => $entry->title]) }}">
                                @if ($coverUrl)
                                    <img src="{{ $coverUrl }}" alt="{{ $cover->alt_text ?: $entry->title }}" loading="lazy">
                                @else
                                    <span class="image-placeholder" aria-hidden="true"><span>{{ mb_substr($entry->title, 0, 1) }}</span></span>
                                @endif
                                @if ($entry->categories->isNotEmpty())
                                    <span class="featured-category">{{ $entry->categories->first()->name }}</span>
                                @endif
                            </a>
                            <div class="featured-card-copy">
                                <span class="featured-region">{{ $entry->region?->name ?? 'Nusantara' }}</span>
                                <h3><a href="{{ route('cultural-entries.index', ['search' => $entry->title]) }}">{{ $entry->title }}</a></h3>
                                <p>{{ $entry->short_description }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="curation-note">
                    <div class="curation-image">
                        <img src="{{ asset('images/culture/borobudur.jpg') }}" alt="Stupa dan relief Candi Borobudur, Jawa Tengah" loading="lazy" decoding="async">
                        <a href="https://commons.wikimedia.org/wiki/File:Borobudur-Temple-Park_Indonesia_Stupas-of-Borobudur-01.jpg" target="_blank" rel="noopener noreferrer">Photo by CEphoto, Uwe Aranas · CC BY-SA 3.0</a>
                    </div>
                    <div>
                        <span class="curation-kicker">Ruang koleksi</span>
                        <h3>Warisan yang menunggu untuk diceritakan.</h3>
                        <p>Entri pilihan akan hadir setelah lolos kurasi dan media mendapatkan persetujuan. Sementara itu, jelajahi sepuluh jendela budaya Nusantara di atas.</p>
                    </div>
                    <a class="text-link" href="{{ route('cultural-entries.index') }}">Kunjungi katalog <span aria-hidden="true">↗</span></a>
                </div>
            @endif
        </div>
    </section>

    <section class="story-section" id="cerita" aria-labelledby="story-title">
        <div class="page-shell story-panel">
            <div class="story-copy">
                <p class="eyebrow eyebrow-light"><span class="eyebrow-line"></span> Cerita Nusantara</p>
                <h2 id="story-title">Warisan bukan sekadar masa lalu. Ia adalah <em>cerita yang terus hidup.</em></h2>
                <p>Kenali makna di balik tradisi, karya, dan pengetahuan yang membentuk keseharian masyarakat Indonesia.</p>
                <a class="button button-outline-light" href="{{ route('cultural-entries.index') }}">Temukan kisah budaya <span aria-hidden="true">↗</span></a>
            </div>
            <figure class="story-image">
                <img src="{{ asset('images/culture/borobudur.jpg') }}" alt="Stupa Candi Borobudur, salah satu warisan arsitektur bersejarah Indonesia" loading="lazy" decoding="async">
                <figcaption>
                    <span>Borobudur · Jawa Tengah</span>
                    <a href="https://commons.wikimedia.org/wiki/File:Borobudur-Temple-Park_Indonesia_Stupas-of-Borobudur-01.jpg" target="_blank" rel="noopener noreferrer">Photo by CEphoto, Uwe Aranas · CC BY-SA 3.0</a>
                </figcaption>
            </figure>
        </div>
    </section>
@endsection
