<article class="product-card">
    <a class="product-image" href="{{ route('products.show', $product) }}">
        <span class="product-badge">{{ $product->category === 'frozen' ? 'FROZEN' : 'FAVORIT' }}</span>
        <img src="{{ $product->image_url ?: 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=900&q=85' }}" alt="{{ $product->name }}" loading="lazy">
        @if($product->stock > 0)<span class="quick-view">Lihat produk <span>↗</span></span>@endif
    </a>
    <div class="product-info">
        <div class="product-title-row"><a href="{{ route('products.show', $product) }}"><h3>{{ $product->name }}</h3></a><span class="product-category">{{ $product->category === 'frozen' ? 'Frozen' : 'Snack' }}</span></div>
        <p class="product-unit">{{ $product->unit }}</p>
        <div class="product-buy-row"><strong>Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
            @if($product->stock > 0)
                <form method="POST" action="{{ route('cart.add', $product) }}">@csrf<input type="hidden" name="quantity" value="1"><button class="add-button" type="submit" aria-label="Tambah {{ $product->name }} ke keranjang">+</button></form>
            @else
                <span class="sold-out">Habis</span>
            @endif
        </div>
    </div>
</article>
