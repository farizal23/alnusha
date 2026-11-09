@extends('layouts.app')

@section('title', ($category === 'snack' ? 'Snack' : ($category === 'frozen' ? 'Frozen food' : 'Semua produk')).' — Al Nusha')

@section('content')
    @include('shop.partials.store-hero', [
        'breadcrumbs' => true,
        'heroEyebrow' => $category === 'snack' ? 'SNACK PILIHAN AL NUSHA' : ($category === 'frozen' ? 'FROZEN FOOD FAVORIT' : 'PRODUK PILIHAN AL NUSHA'),
        'heroTitle' => $category === 'snack' ? 'Camilan seru,' : ($category === 'frozen' ? 'Praktis untuk' : 'Temukan yang'),
        'heroAccent' => $category === 'snack' ? 'teman setiap hari.' : ($category === 'frozen' ? 'setiap momen.' : 'kamu suka.'),
        'heroDescription' => $category === 'snack'
            ? 'Jelajahi snack renyah favorit untuk menemani santai, kumpul, dan momen spesial.'
            : ($category === 'frozen'
                ? 'Stok frozen food praktis yang mudah disiapkan untuk camilan dan hidangan keluarga.'
                : 'Semua snack dan frozen food Al Nusha ada di sini. Cari favoritmu dan pilih sesuai kebutuhan.'),
        'heroPrimaryUrl' => '#produk-list',
        'heroPrimaryLabel' => 'Lihat produk',
        'heroSecondaryUrl' => route('about'),
        'heroSecondaryLabel' => 'Tentang Al Nusha',
        'heroImage' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?auto=format&fit=crop&w=1100&q=90',
        'heroImageAlt' => 'Pangsit pilihan untuk camilan keluarga',
    ])

    <section class="section collection-section catalog-section" id="produk-list">
        <div class="container">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">{{ $search ? 'HASIL PENCARIAN' : 'PRODUK AL NUSHA' }}</span>
                    <h2>{{ $search ? 'Hasil untuk “'.$search.'”' : ($category === 'snack' ? 'Semua snack' : ($category === 'frozen' ? 'Semua frozen food' : 'Semua produk')) }}</h2>
                    <p>{{ $products->total() }} produk tersedia untukmu.</p>
                </div>
                <div class="collection-filter" aria-label="Pilih halaman katalog">
                    <a class="{{ !$category ? 'active' : '' }}" href="{{ route('catalog.index') }}">Semua</a>
                    <a class="{{ $category === 'snack' ? 'active' : '' }}" href="{{ route('catalog.index', ['kategori' => 'snack']) }}">Snack</a>
                    <a class="{{ $category === 'frozen' ? 'active' : '' }}" href="{{ route('catalog.index', ['kategori' => 'frozen']) }}">Frozen</a>
                </div>
            </div>
            @if($products->isEmpty())
                <div class="empty-state"><span>⌕</span><h3>Belum ada produk yang cocok</h3><p>Coba kata kunci lain, atau lihat semua koleksi kami.</p><a class="button button-dark" href="{{ route('catalog.index') }}">Lihat semua produk</a></div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        @include('shop.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="pagination-wrap">{{ $products->links() }}</div>
            @endif
        </div>
    </section>
@endsection
