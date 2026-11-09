@extends('admin.layout')
@section('title', 'Dashboard — Al Nusha')

@section('content')
    <div class="container admin-container">
        <div class="admin-heading"><div><span class="eyebrow">RINGKASAN TOKO</span><h1>Selamat datang 👋</h1><p>Pantau toko Al Nusha dan buka halaman pengelolaan yang kamu butuhkan.</p></div></div>
        <div class="stats-grid">
            <a class="stat-card stat-link" href="{{ route('admin.products.index') }}"><span class="stat-icon stat-mint">▦</span><span>Total produk</span><strong>{{ $stats['products'] }}</strong><small>Lihat dan kelola semua produk →</small></a>
            <article class="stat-card"><span class="stat-icon stat-peach">◷</span><span>Stok menipis</span><strong>{{ $stats['low_stock'] }}</strong><small>Produk dengan stok di bawah 10</small></article>
            <a class="stat-card stat-link" href="{{ route('admin.orders.index') }}"><span class="stat-icon stat-lilac">↗</span><span>Pesanan masuk</span><strong data-stat-value="orders">{{ $stats['orders'] }}</strong><small>{{ $stats['pending_orders'] }} menunggu konfirmasi →</small></a>
            <article class="stat-card"><span class="stat-icon stat-yellow">Rp</span><span>Nilai pesanan berjalan</span><strong class="stat-revenue">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</strong><small>Pesanan diproses sampai selesai</small></article>
        </div>
        <div class="admin-welcome-grid">
            <a class="admin-shortcut" href="{{ route('admin.products.index') }}"><span class="shortcut-icon stat-mint">▦</span><span><strong>Kelola katalog produk</strong><small>Atur harga, stok, dan produk yang tampil di toko.</small></span><span class="shortcut-arrow">↗</span></a>
            <a class="admin-shortcut" href="{{ route('admin.orders.index') }}"><span class="shortcut-icon stat-lilac">↗</span><span><strong>Kelola pesanan pelanggan</strong><small>Lihat detail dan perbarui status pesanan.</small></span><span class="shortcut-arrow">↗</span></a>
        </div>
    </div>
@endsection
