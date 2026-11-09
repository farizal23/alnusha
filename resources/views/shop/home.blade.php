@extends('layouts.app')

@section('title', 'Al Nusha — Camilan baik, momen nikmat')

@section('content')
    @include('shop.partials.store-hero', [
        'heroEyebrow' => 'TEMAN SERU SETIAP HARI',
        'heroTitle' => 'Camilan baik,',
        'heroAccent' => 'momen nikmat.',
        'heroDescription' => 'Dari snack renyah sampai frozen food praktis. Pilihan favorit yang bikin kumpul keluarga dan bisnis makin berarti.',
        'heroPrimaryUrl' => route('catalog.index'),
        'heroPrimaryLabel' => 'Lihat semua produk',
        'heroSecondaryUrl' => route('about'),
        'heroSecondaryLabel' => 'Cerita Al Nusha',
        'heroImageAlt' => 'Camilan kentang renyah',
    ])

    @if($featuredProducts->isNotEmpty())
        <section class="section home-featured">
            <div class="container">
                <div class="section-heading">
                    <div><span class="eyebrow">PILIHAN KAMI UNTUKMU</span><h2>Mulai dari yang favorit</h2><p>Beberapa pilihan yang paling pas untuk menemani harimu.</p></div>
                    <a class="text-link" href="{{ route('catalog.index') }}">Lihat semua produk <span>↗</span></a>
                </div>
                <div class="product-grid">
                    @foreach($featuredProducts as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="bulk-banner" id="pesanan-partai">
        <div class="container bulk-inner">
            <div class="bulk-icon">✳</div>
            <div><span class="eyebrow">UNTUK KAMU YANG PUNYA USAHA</span><h2>Belanja banyak? Kita atur bareng.</h2><p>Pesanan dalam jumlah besar bisa mendapatkan harga yang disesuaikan. Ceritakan kebutuhanmu kepada kami.</p></div>
            <a class="button button-light" href="{{ route('about') }}#pesanan-partai">Info pesanan besar <span>↗</span></a>
        </div>
    </section>
@endsection
