@extends('layouts.app')

@section('title', 'Checkout — Al Nusha')

@section('content')
    <section class="section checkout-section"><div class="container narrow-container">
        <div class="page-heading"><span class="eyebrow">PESAN DENGAN MUDAH</span><h1>Checkout</h1><p>Isi detail penerima agar pesananmu sampai dengan nyaman.</p></div>
        <form class="checkout-layout" method="POST" action="{{ route('checkout.store') }}">@csrf
            <div class="checkout-fields">
                <section class="form-card"><div class="form-card-heading"><span>01</span><div><h2>Informasi penerima</h2><p>Detail untuk konfirmasi dan pengiriman pesanan.</p></div></div>
                    <label class="field-label" for="customer_name">Nama lengkap</label><input class="form-input" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required maxlength="120" autocomplete="name" placeholder="Nama penerima">
                    <div class="field-row"><div><label class="field-label" for="customer_email">Email</label><input class="form-input" id="customer_email" type="email" name="customer_email" value="{{ old('customer_email') }}" required maxlength="180" autocomplete="email" placeholder="nama@email.com"></div><div><label class="field-label" for="customer_phone">Nomor WhatsApp</label><input class="form-input" id="customer_phone" type="tel" name="customer_phone" value="{{ old('customer_phone') }}" required maxlength="30" autocomplete="tel" placeholder="08xx xxxx xxxx"></div></div>
                    <label class="field-label" for="shipping_address">Alamat lengkap</label><textarea class="form-input" id="shipping_address" name="shipping_address" rows="3" required maxlength="2000" autocomplete="street-address" placeholder="Jalan, nomor rumah, kelurahan, kecamatan, kota, kode pos">{{ old('shipping_address') }}</textarea>
                    <label class="field-label" for="customer_note">Catatan untuk pesanan <span class="optional-label">Opsional</span></label><textarea class="form-input" id="customer_note" name="customer_note" rows="2" maxlength="1000" placeholder="Patokan alamat atau permintaan khusus">{{ old('customer_note') }}</textarea>
                </section>
                <section class="form-card"><div class="form-card-heading"><span>02</span><div><h2>Pilih pembayaran</h2><p>Bayar terlebih dahulu, pesanan diproses setelah pembayaran diverifikasi.</p></div></div>
                    <label class="payment-option"><input type="radio" name="payment_method" value="qris" {{ old('payment_method', 'qris') === 'qris' ? 'checked' : '' }}><span class="payment-symbol">▦</span><span><strong>QRIS <span class="demo-badge">DEMO</span></strong><small>QRIS resmi belum ditambahkan. Minta instruksi pembayaran melalui WhatsApp.</small></span></label>
                    <label class="payment-option"><input type="radio" name="payment_method" value="transfer" {{ old('payment_method', 'qris') === 'transfer' ? 'checked' : '' }}><span class="payment-symbol">↗</span><span><strong>Transfer bank <span class="demo-badge">DEMO</span></strong><small>Rekening resmi belum ditambahkan. Minta instruksi pembayaran melalui WhatsApp.</small></span></label>
                    <div class="payment-notice">Jangan transfer atau membayar dengan QR yang tidak diberikan langsung oleh admin Al Nusha.</div>
                </section>
            </div>
            <aside class="summary-card checkout-summary"><h2>Pesananmu</h2>
                @foreach($items as $item)<div class="checkout-item"><span class="checkout-item-qty">{{ $item['quantity'] }}×</span><span>{{ $item['product']->name }}</span><strong>Rp {{ number_format($item['line_total'], 0, ',', '.') }}</strong></div>@endforeach
                <div class="summary-line"><span>Subtotal</span><strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong></div>
                <div class="summary-line"><span>Estimasi pengiriman</span><strong>Rp {{ number_format($shippingCost, 0, ',', '.') }}</strong></div>
                <p class="summary-hint">Biaya pengiriman dapat berubah untuk produk frozen dan pesanan partai besar. Kami akan konfirmasi sebelum pesanan diproses.</p>
                <div class="summary-total"><span>Total sementara</span><strong>Rp {{ number_format($subtotal + $shippingCost, 0, ',', '.') }}</strong></div>
                <button class="button button-dark button-wide" type="submit">Buat pesanan <span>↗</span></button>
                <p class="secure-note">✓ Kirim bukti pembayaran melalui WhatsApp. Admin akan memverifikasi sebelum pesanan diproses.</p>
            </aside>
        </form>
    </div></section>
@endsection
