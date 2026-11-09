<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#183c35">
    <meta name="description" content="Al Nusha — snack dan frozen food pilihan untuk keluarga, toko, dan usaha kuliner Anda.">
    <title>@yield('title', 'Al Nusha — Camilan baik, momen nikmat')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body style="--page-background-image: url('{{ asset('images/latar_belakang.jpg') }}')">
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="Al Nusha, halaman utama">
                <span class="brand-mark">an<span>.</span></span>
                <span class="brand-copy"><strong>al nusha</strong><small>GOOD FOOD, GOOD MOOD</small></span>
            </a>
            <nav class="main-nav" id="main-nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Beranda</a>
                <a href="{{ route('catalog.index') }}" @class(['active' => request()->routeIs('catalog.*') || request()->routeIs('products.show')])>Produk</a>
                <a href="{{ route('about') }}" @class(['active' => request()->routeIs('about')])>Tentang kami</a>
                <a href="{{ route('contact') }}" @class(['active' => request()->routeIs('contact*')])>Kontak</a>
                <a href="{{ route('orders.track') }}" @class(['active' => request()->routeIs('orders.track', 'orders.find')])>Lacak Pesanan</a>
            </nav>
            <div class="header-actions">
                <button class="menu-toggle" type="button" aria-label="Buka menu navigasi" aria-expanded="false" aria-controls="main-nav" data-nav-toggle>
                    <span></span><span></span><span></span>
                </button>
                <form action="{{ route('catalog.index') }}" method="GET" class="header-search">
                    <span aria-hidden="true">⌕</span>
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari produk..." aria-label="Cari produk">
                </form>
                <a class="cart-link" href="{{ route('cart.index') }}" aria-label="Keranjang belanja">
                    <span aria-hidden="true">♧</span><span class="cart-label">Keranjang</span>
                    @if(session('cart'))
                        <span class="cart-count">{{ collect(session('cart'))->sum() }}</span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    @if(session('success') || session('error') || $errors->any())
        <div class="container flash-wrap">
            @if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="flash flash-error">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="flash flash-error">{{ $errors->first() }}</div>@endif
        </div>
    @endif

    <main>@yield('content')</main>

    <footer class="site-footer" id="tentang">
        <div class="container footer-grid">
            <div>
                <a class="brand footer-brand" href="{{ route('home') }}">
                    <span class="brand-mark">an<span>.</span></span>
                    <span class="brand-copy"><strong>al nusha</strong><small>GOOD FOOD, GOOD MOOD</small></span>
                </a>
                <p>Camilan seru dan frozen food favorit, dipilih untuk bikin momen kecil jadi lebih nikmat.</p>
            </div>
            <div class="footer-links">
                <strong>Jelajahi</strong>
                <a href="{{ route('catalog.index') }}">Semua produk</a>
                <a href="{{ route('catalog.index', ['kategori' => 'snack']) }}">Snack pilihan</a>
                <a href="{{ route('catalog.index', ['kategori' => 'frozen']) }}">Frozen food</a>
                <a href="{{ route('about') }}">Tentang kami</a>
                <a href="{{ route('contact') }}">Kontak &amp; kerja sama</a>
                <a href="{{ route('orders.track') }}">Lacak Pesanan</a>
            </div>
            <div class="footer-contact">
                <strong>Hubungi Al Nusha</strong>
                <p>Untuk pesanan, pertanyaan, atau peluang kerja sama, tim kami siap membantu.</p>
                <a class="footer-contact-phone" href="tel:{{ config('shop.contact_phone') }}">{{ config('shop.contact_phone') }}</a>
                <a class="text-link" href="https://wa.me/{{ config('shop.whatsapp_number') }}">Chat via WhatsApp <span>↗</span></a>
            </div>
            <div class="footer-socials">
                <strong>Media sosial</strong>
                <div class="footer-social-list"><span>Instagram</span><span>Facebook</span><span>TikTok</span></div>
                <a class="footer-location" href="https://www.google.com/maps/search/?api=1&amp;query=Raja+CCTV">Lokasi: Raja CCTV <span>↗</span></a>
            </div>
        </div>
        <div class="container footer-bottom"><span>© {{ date('Y') }} Al Nusha. Dibuat dengan sepenuh hati.</span><a href="{{ route('admin.login') }}">Admin</a></div>
    </footer>
</body>
</html>