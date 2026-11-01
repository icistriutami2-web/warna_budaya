<footer class="site-footer" id="tentang">
    <div class="page-shell footer-main">
        <div class="footer-brand">
            <a class="brand brand-light" href="{{ route('home') }}">
                <span class="brand-mark" aria-hidden="true">W</span>
                <span class="brand-copy">
                    <span class="brand-name">WARNA BUDAYA</span>
                    <span class="brand-tagline">Kenali · Jelajahi · Lestarikan</span>
                </span>
            </a>
            <p>Ruang digital untuk mengenal, merayakan, dan menjaga kekayaan budaya Indonesia.</p>
        </div>
        <div class="footer-column">
            <h2>Jelajahi</h2>
            <a href="{{ route('regions.index') }}">Jelajah Nusantara</a>
            <a href="{{ route('home') }}#kategori">Kategori budaya</a>
            <a href="{{ route('cultural-entries.index') }}">Arsip budaya</a>
        </div>
        <div class="footer-column">
            <h2>Tentang platform</h2>
            <a href="{{ route('home') }}#tentang">Misi kami</a>
            <a href="{{ route('home') }}#cerita">Cerita Nusantara</a>
            <span class="footer-note">Berangkat dari pengetahuan dan sumber yang dapat ditelusuri.</span>
            @if (request()->routeIs('home'))
                <a class="footer-superadmin-link" href="{{ route('admin.super-entry') }}" aria-label="Akses khusus Super Admin">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 19 6v5c0 4.7-2.9 8.1-7 10-4.1-1.9-7-5.3-7-10V6l7-3Z"></path><path d="m9.5 12 1.7 1.7 3.5-3.8"></path></svg>
                    <span>Akses Super Admin</span>
                </a>
            @endif
        </div>
    </div>
    <div class="page-shell footer-bottom">
        <span>© {{ date('Y') }} WARNA BUDAYA</span>
        <span>Merawat cerita, menjaga warisan.</span>
    </div>
</footer>
