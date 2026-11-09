@extends('layouts.app')

@section('title', 'Keranjang belanja — Al Nusha')

@section('content')
    <section class="section cart-section"><div class="container narrow-container">
        <div class="page-heading"><span class="eyebrow">TINGGAL SELANGKAH LAGI</span><h1>Keranjang belanja</h1><p>Periksa pilihan lezatmu sebelum melanjutkan.</p></div>
        @if($items->isEmpty())
            <div class="empty-state cart-empty"><span>♧</span><h3>Keranjangmu masih kosong</h3><p>Yuk, temukan camilan favorit untuk menemani harimu.</p><a class="button button-dark" href="{{ route('catalog.index') }}">Mulai belanja <span>↗</span></a></div>
        @else
            <div class="cart-layout"><div class="cart-items">
                @foreach($items as $item)
                    <article class="cart-item">
                        <a class="cart-item-image" href="{{ route('products.show', $item['product']) }}"><img src="{{ $item['product']->image_url ?: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85' }}" alt="{{ $item['product']->name }}"></a>
                        <div class="cart-item-main"><a href="{{ route('products.show', $item['product']) }}"><h3>{{ $item['product']->name }}</h3></a><p>{{ $item['product']->unit }} · Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                            <div class="cart-item-actions">
                                <form class="quantity-form" method="POST" action="{{ route('cart.update', $item['product']) }}">@csrf @method('PATCH')<label for="qty-{{ $item['product']->id }}">Jumlah</label><input id="qty-{{ $item['product']->id }}" type="number" name="quantity" min="1" max="{{ $item['product']->stock }}" value="{{ $item['quantity'] }}"><button type="submit">Perbarui</button></form>
                                <form method="POST" action="{{ route('cart.remove', $item['product']) }}">@csrf @method('DELETE')<button class="remove-link" type="submit">Hapus</button></form>
                            </div>
                        </div>
                        <strong class="cart-line-price">Rp {{ number_format($item['line_total'], 0, ',', '.') }}</strong>
                    </article>
                @endforeach
            </div><aside class="summary-card">
                <h2>Ringkasan belanja</h2>
                <div class="summary-line"><span>Subtotal</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                <div class="summary-line"><span>Estimasi pengiriman</span><strong>Rp {{ number_format($shippingCost, 0, ',', '.') }}</strong></div>
                <p class="summary-hint">Ongkir frozen food dan pesanan besar dapat dikonfirmasi kembali oleh tim kami.</p>
                <div class="summary-total"><span>Total sementara</span><strong>Rp {{ number_format($subtotal + $shippingCost, 0, ',', '.') }}</strong></div>
                <a class="button button-dark button-wide" href="{{ route('checkout') }}">Lanjut ke checkout <span>↗</span></a>
                <a class="back-to-shop" href="{{ route('catalog.index') }}">← Lanjut belanja</a>
            </aside></div>
        @endif
    </div></section>
@endsection
