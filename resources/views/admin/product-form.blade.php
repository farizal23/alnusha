@extends('admin.layout')
@section('title', ($product->exists ? 'Ubah produk' : 'Tambah produk').' — Al Nusha')

@section('content')
    <div class="container admin-container">
        <a class="back-to-shop admin-back" href="{{ route('admin.products.index') }}">← Kembali ke produk</a>
        <div class="admin-heading form-page-heading"><div><span class="eyebrow">KATALOG AL NUSHA</span><h1>{{ $product->exists ? 'Ubah informasi produk' : 'Tambah produk baru' }}</h1><p>Lengkapi detail produk agar pelanggan tahu apa yang mereka pilih.</p></div></div>
        <form class="product-editor" method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
            @csrf @if($product->exists) @method('PUT') @endif
            <div class="editor-main form-card">
                <label class="field-label" for="name">Nama produk</label><input class="form-input" id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="180" placeholder="Contoh: Basreng pedas">
                <div class="field-row"><div><label class="field-label" for="category">Kategori</label><select class="form-input" id="category" name="category" required><option value="snack" @selected(old('category', $product->category) === 'snack')>Snack</option><option value="frozen" @selected(old('category', $product->category) === 'frozen')>Frozen food</option></select></div><div><label class="field-label" for="unit">Satuan / keterangan</label><input class="form-input" id="unit" name="unit" value="{{ old('unit', $product->unit ?: 'per pack') }}" required maxlength="80" placeholder="1 ball, 1 kg, per pack"></div></div>
                <div class="field-row"><div><label class="field-label" for="price">Harga jual (Rp)</label><input class="form-input" id="price" type="number" name="price" value="{{ old('price', $product->price) }}" min="1" required placeholder="73000"></div><div><label class="field-label" for="stock">Stok tersedia</label><input class="form-input" id="stock" type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required placeholder="20"></div></div>
                <label class="field-label" for="description">Deskripsi produk <span class="optional-label">Opsional</span></label><textarea class="form-input" id="description" name="description" rows="4" maxlength="2000" placeholder="Ceritakan keistimewaan produk ini.">{{ old('description', $product->description) }}</textarea>
                <label class="field-label" for="image_url">URL foto produk <span class="optional-label">Opsional</span></label><input class="form-input" id="image_url" type="url" name="image_url" value="{{ old('image_url', $product->image_url) }}" maxlength="2048" placeholder="https://...">
            </div>
            <aside class="editor-sidebar form-card">
                <h2>Pengaturan katalog</h2><p>Pilih produk yang ditampilkan kepada pelanggan.</p>
                <label class="toggle-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))><span><strong>Tampilkan di toko</strong><small>Pelanggan dapat melihat dan membeli produk.</small></span></label>
                <label class="toggle-row"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))><span><strong>Produk pilihan</strong><small>Tampilkan sebagai pilihan favorit.</small></span></label>
                <button class="button button-dark button-wide" type="submit">{{ $product->exists ? 'Simpan perubahan' : 'Tambah produk' }} <span>↗</span></button>
                <a class="back-to-shop editor-cancel" href="{{ route('admin.products.index') }}">Batal</a>
            </aside>
        </form>
    </div>
@endsection
