@extends('layouts.app')

@section('title', 'Tentang Al Nusha — Camilan baik, momen nikmat')

@section('content')
    <section class="about-hero">
        <div class="container about-hero-grid">
            <div class="about-hero-copy">
                <div class="breadcrumbs"><a href="{{ route('home') }}">Beranda</a><span>/</span><span>Tentang kami</span></div>
                <span class="eyebrow">CERITA DI BALIK SETIAP PILIHAN</span>
                <h1>Hal kecil yang bikin momen terasa <em>lebih dekat.</em></h1>
                <p>Al Nusha menghadirkan snack dan frozen food pilihan untuk keluarga, toko, dan usaha kuliner. Praktis dipilih, menyenangkan dibagikan.</p>
                <a class="button button-dark" href="{{ route('catalog.index') }}">Jelajahi produk <span>↗</span></a>
            </div>
            <div class="about-photo"><img src="https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=1100&q=90" alt="Hidangan yang dibagikan bersama keluarga"><span class="about-photo-note">Dipilih untuk momen bersama</span></div>
        </div>
    </section>

    <section class="section about-story">
        <div class="container about-story-grid">
            <div><span class="eyebrow">APA YANG KAMI PERCAYA</span><h2>Rasa enak terasa lebih baik saat dibagikan.</h2></div>
            <div><p>Karena setiap rumah dan usaha punya kebutuhan yang berbeda, kami menyediakan pilihan snack dan frozen food yang beragam. Dari teman santai sore sampai stok praktis untuk dapur usaha.</p><p>Kami percaya belanja yang nyaman dimulai dari pilihan yang jelas, harga yang transparan, dan komunikasi yang mudah. Untuk pesanan dalam jumlah besar, harga bisa disesuaikan kembali.</p></div>
        </div>
    </section>

    <section class="about-values">
        <div class="container about-values-grid">
            <article><span>01</span><h3>Pilihan yang beragam</h3><p>Temukan snack dan frozen food dalam satu tempat untuk momen yang berbeda.</p></article>
            <article><span>02</span><h3>Belanja yang praktis</h3><p>Jelajahi katalog, pilih produk, dan kirim pesanan dengan langkah sederhana.</p></article>
            <article><span>03</span><h3>Fleksibel untuk usaha</h3><p>Butuh jumlah lebih banyak? Harga dapat dibicarakan menyesuaikan kebutuhan.</p></article>
        </div>
    </section>

    <section class="bulk-banner about-bulk" id="pesanan-partai">
        <div class="container bulk-inner">
            <div class="bulk-icon">✳</div>
            <div><span class="eyebrow">PESANAN PARTAI BESAR</span><h2>Punya kebutuhan khusus atau pesanan banyak?</h2><p>Harga dan pilihan produk bisa disesuaikan kembali. Hubungi tim Al Nusha untuk membicarakan kebutuhanmu.</p></div>
            <a class="button button-light" href="{{ route('contact') }}">Hubungi kami <span>↗</span></a>
        </div>
    </section>
@endsection
