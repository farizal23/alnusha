<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            @if(!empty($breadcrumbs))
                <div class="breadcrumbs hero-breadcrumbs"><a href="{{ route('home') }}">Beranda</a><span>/</span><span>Produk</span></div>
            @endif
            <span class="eyebrow"><span class="eyebrow-dot"></span> {{ $heroEyebrow }}</span>
            <h1>{{ $heroTitle }}<br><em>{{ $heroAccent }}</em></h1>
            <p>{{ $heroDescription }}</p>
            <div class="hero-actions">
                <a class="button button-dark" href="{{ $heroPrimaryUrl }}">{{ $heroPrimaryLabel }} <span>↗</span></a>
                <a class="hero-link" href="{{ $heroSecondaryUrl }}">{{ $heroSecondaryLabel }} <span>→</span></a>
            </div>
            <div class="hero-proof">
                <div class="proof-avatars"><span>☺</span><span>✿</span><span>♥</span></div>
                <div><strong>Teman belanja keluarga</strong><small>Rasa yang selalu bikin kembali</small></div>
            </div>
        </div>
        <div class="hero-art">
            <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
            <div class="hero-photo">
                <img src="{{ $heroImage ?? 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=1100&q=90' }}" alt="{{ $heroImageAlt ?? 'Camilan pilihan Al Nusha' }}">
            </div>
            <div class="hero-note note-top"><span class="note-icon">✳</span><span><strong>{{ $snackCount + $frozenCount }} pilihan</strong><small>snack & frozen food</small></span></div>
            <div class="hero-note note-bottom"><span class="note-icon note-orange">♡</span><span><strong>Pesan mudah</strong><small>langsung dari toko kami</small></span></div>
            <span class="hero-sparkle sparkle-a">✳</span><span class="hero-sparkle sparkle-b">✴</span>
        </div>
    </div>
    <div class="container hero-categories">
        <a href="{{ route('catalog.index', ['kategori' => 'snack']) }}"><span class="category-emoji">🥨</span><span><strong>Snack favorit</strong><small>{{ $snackCount }} pilihan, renyah di setiap gigitan</small></span><span class="category-arrow">↗</span></a>
        <a href="{{ route('catalog.index', ['kategori' => 'frozen']) }}"><span class="category-emoji">❄️</span><span><strong>Frozen food</strong><small>{{ $frozenCount }} pilihan praktis untuk kapan saja</small></span><span class="category-arrow">↗</span></a>
        <a href="{{ route('about') }}#pesanan-partai"><span class="category-emoji">✦</span><span><strong>Belanja jumlah besar</strong><small>Harga bisa disesuaikan</small></span><span class="category-arrow">↗</span></a>
    </div>
</section>
