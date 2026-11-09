@extends('admin.layout')
@section('title', 'Kelola produk — Al Nusha')

@section('content')
    <div class="container admin-container">
        <div class="admin-heading"><div><span class="eyebrow">KATALOG TOKO</span><h1>Kelola produk</h1><p>Atur harga, stok, dan produk yang ditampilkan kepada pelanggan.</p></div><a class="button button-dark" href="{{ route('admin.products.create') }}">＋ Tambah produk</a></div>
        <section class="admin-panel">
            <div class="admin-panel-heading"><div><h2>Daftar produk</h2><p>{{ $products->total() }} produk terdaftar.</p></div></div>
            <form class="admin-filters" method="GET" action="{{ route('admin.products.index') }}">
                <input class="form-input" type="search" name="q" value="{{ $search }}" placeholder="Cari nama produk..." aria-label="Cari nama produk">
                <select class="form-input" name="kategori" aria-label="Filter kategori"><option value="">Semua kategori</option><option value="snack" @selected($category === 'snack')>Snack</option><option value="frozen" @selected($category === 'frozen')>Frozen food</option></select>
                <button class="button button-dark" type="submit">Terapkan filter</button>
                @if($search || $category)<a class="admin-clear-filter" href="{{ route('admin.products.index') }}">Hapus filter</a>@endif
            </form>
            <div class="table-scroll"><table class="admin-table"><thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Visibilitas</th><th>Aksi</th></tr></thead><tbody>
                @forelse($products as $product)
                    <tr><td><div class="admin-product-name"><img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85' }}" alt=""><strong>{{ $product->name }}</strong></div></td><td>{{ $product->category === 'frozen' ? 'Frozen food' : 'Snack' }}</td><td class="table-price">Rp {{ number_format($product->price, 0, ',', '.') }}<small>{{ $product->unit }}</small></td><td><span class="{{ $product->stock < 10 ? 'stock-low' : '' }}">{{ $product->stock }} {{ $product->unit }}</span></td><td><span class="status-pill {{ $product->is_active ? 'status-selesai' : 'status-dibatalkan' }}">{{ $product->is_active ? 'Aktif' : 'Disembunyikan' }}</span></td><td><div class="table-actions"><a href="{{ route('admin.products.edit', $product) }}">Ubah</a><form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus {{ $product->name }} dari katalog?')">@csrf @method('DELETE')<button type="submit">Hapus</button></form></div></td></tr>
                @empty
                    <tr><td colspan="6"><div class="admin-empty">Tidak ada produk yang cocok dengan filter ini.</div></td></tr>
                @endforelse
            </tbody></table></div>
            <div class="admin-pagination">{{ $products->links() }}</div>
        </section>
    </div>
@endsection
