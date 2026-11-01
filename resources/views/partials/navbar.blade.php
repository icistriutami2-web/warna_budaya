<header class="site-header">
    <nav class="navigation page-shell" aria-label="Navigasi utama">
        <a class="brand" href="{{ route('home') }}" aria-label="WARNA BUDAYA, Beranda">
            <span class="brand-mark" aria-hidden="true">W</span>
            <span class="brand-copy">
                <span class="brand-name">WARNA BUDAYA</span>
                <span class="brand-tagline">Kenali · Jelajahi · Lestarikan</span>
            </span>
        </a>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu" aria-label="Buka menu navigasi">
            <span></span><span></span>
        </button>

        <div class="navigation-menu" id="primary-menu">
            <div class="navigation-links">
                <a class="navigation-link" href="{{ route('home') }}">Beranda</a>
                <a class="navigation-link" href="{{ route('regions.index') }}">Jelajah Nusantara</a>
                <a class="navigation-link" href="{{ route('home') }}#kategori">Kategori Budaya</a>
                <a class="navigation-link" href="{{ route('cultural-entries.index') }}">Arsip Budaya</a>
                <a class="navigation-link" href="{{ route('home') }}#cerita">Cerita Nusantara</a>
                <a class="navigation-link" href="{{ route('home') }}#tentang">Tentang Kami</a>
            </div>
            <div class="navigation-actions">
                <a class="icon-link" href="{{ route('cultural-entries.index') }}" aria-label="Cari arsip budaya">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 4.5 4.5"></path></svg>
                </a>
                <a class="navigation-cta" href="{{ route('regions.index') }}">Mulai Jelajah <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </nav>
</header>
