@extends('layouts.app')

@section('title', $product->name.' — Al Nusha')

@section('content')
    <section class="section product-detail-section">
        <div class="container">
            <div class="breadcrumbs"><a href="{{ route('home') }}">Beranda</a><span>/</span><a href="{{ route('home', ['kategori' => $product->category]) }}">{{ $product->category === 'frozen' ? 'Frozen food' : 'Snack' }}</a><span>/</span><span>{{ $product->name }}</span></div>
            <div class="product-detail">
                <div class="detail-image"><img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85' }}" alt="{{ $product->name }}"></div>
                <div class="detail-copy">
                    <span class="eyebrow">{{ $product->category === 'frozen' ? 'FROZEN FOOD PILIHAN' : 'SNACK PILIHAN' }}</span>
                    <h1>{{ $product->name }}</h1>
                    <p class="detail-price">Rp {{ number_format($product->price, 0, ',', '.') }} <small>/ {{ $product->unit }}</small></p>
                    <p class="detail-description">{{ $product->description ?: 'Pilihan favorit Al Nusha untuk melengkapi momen nikmatmu.' }}</p>
                    <div class="stock-note"><span class="stock-dot"></span> Tersedia · {{ $product->stock }} {{ $product->unit }}</div>
                    <form class="detail-add-form" method="POST" action="{{ route('cart.add', $product) }}">
                        @csrf
                        <label for="quantity">Jumlah</label>
                        <input id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min($product->stock, 99) }}">
                        <button class="button button-dark" type="submit">Tambah ke keranjang <span>↗</span></button>
                    </form>
                    <div class="detail-perks"><span>✓ Pilihan berkualitas</span><span>✓ Harga transparan</span><span>✓ Aman untuk pesanan</span></div>
                </div>
            </div>
        </div>
    </section>
    @if($relatedProducts->isNotEmpty())
        <section class="section related-section"><div class="container"><div class="section-heading"><div><span class="eyebrow">COBA JUGA</span><h2>Teman yang pas</h2></div></div><div class="product-grid">@foreach($relatedProducts as $product)@include('shop.partials.product-card', ['product' => $product])@endforeach</div></div></section>
    @endif
@endsection
