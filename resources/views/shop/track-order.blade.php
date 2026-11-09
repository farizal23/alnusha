@extends('layouts.app')

@section('title', 'Lacak Pesanan — Al Nusha')

@section('content')
    <section class="section tracking-section">
        <div class="container narrow-container">
            <div class="page-heading">
                <span class="eyebrow">STATUS PESANAN</span>
                <h1>Lacak Pesanan</h1>
                <p>Masukkan nomor pesanan dan nomor WhatsApp yang digunakan saat checkout.</p>
            </div>

            <form class="tracking-form form-card" method="POST" action="{{ route('orders.find') }}">
                @csrf
                <label class="field-label" for="order_number">Nomor pesanan</label>
                <input class="form-input" id="order_number" name="order_number" value="{{ old('order_number') }}" required maxlength="32" autocomplete="off" placeholder="Contoh: AN-261108-AB12CD34">
                <label class="field-label" for="customer_phone">Nomor WhatsApp saat checkout</label>
                <input class="form-input" id="customer_phone" name="customer_phone" type="tel" value="{{ old('customer_phone') }}" required maxlength="30" autocomplete="tel" placeholder="Masukkan nomor yang sama dengan checkout">
                <button class="button button-dark button-wide" type="submit">Cari pesanan <span>↗</span></button>
                <p class="secure-note">Nomor pesanan dan nomor WhatsApp dicocokkan untuk menjaga privasi pesanan.</p>
            </form>

            @if($searched && !$order)
                <div class="tracking-result tracking-not-found" role="status">Pesanan tidak ditemukan. Periksa kembali nomor pesanan dan nomor WhatsApp yang digunakan saat checkout.</div>
            @endif

            @if($order)
                <section class="tracking-result tracking-order" aria-label="Detail status pesanan">
                    <div class="tracking-order-heading">
                        <div><span class="eyebrow">NOMOR PESANAN</span><h2>{{ $order->order_number }}</h2>@if($order->is_demo)<span class="demo-badge">DEMO · DATA CONTOH</span>@endif</div>
                        <span class="status-pill status-{{ $order->status }}">{{ $orderStatusLabel }}</span>
                    </div>
                    <p class="tracking-order-date">Dibuat {{ $order->created_at->format('d M Y, H:i') }} · {{ $order->customer_name }}</p>
                    @if($order->is_demo)
                        <div class="payment-notice" role="note">Ini pesanan contoh untuk demonstrasi. Status dan produknya fiktif; tidak ada pembayaran atau pengiriman sungguhan. Jangan menghubungi admin atau membayar untuk pesanan ini.</div>
                    @endif
                    <div class="tracking-payment">
                        <span>Pembayaran {{ $order->payment_method === 'qris' ? 'QRIS' : 'transfer bank' }}{{ $order->is_demo ? ' (DEMO)' : '' }}</span>
                        <strong class="status-pill status-{{ $order->payment_status === 'terverifikasi' ? 'selesai' : 'menunggu' }}">{{ $order->payment_status === 'terverifikasi' ? 'Terverifikasi' : ($order->payment_status === 'menunggu' ? 'Menunggu verifikasi' : 'Belum tercatat') }}</strong>
                    </div>
                    <p class="tracking-description">
                        @if($order->status === 'menunggu')
                            @if($order->payment_status === 'terverifikasi')
                                Pembayaran sudah diverifikasi. Pesanan menunggu admin memulai proses.
                            @elseif($order->payment_status === 'menunggu')
                                Pesanan menunggu pembayaran dan verifikasi admin. Minta instruksi pembayaran resmi melalui WhatsApp sebelum membayar.
                            @else
                                Pesanan menunggu konfirmasi admin. Hubungi admin melalui WhatsApp untuk informasi lebih lanjut.
                            @endif
                        @elseif($order->status === 'diproses')
                            Pembayaran telah diverifikasi dan pesanan sedang disiapkan oleh admin.
                        @elseif($order->status === 'dikirim')
                            Pesanan sedang dikirim. Informasi pengiriman diperbarui manual oleh admin; lokasi kurir langsung tidak tersedia.
                        @elseif($order->status === 'selesai')
                            Pesanan telah ditandai selesai oleh admin.
                        @else
                            Pesanan ini telah dibatalkan. Hubungi admin jika membutuhkan bantuan.
                        @endif
                    </p>
                    <div class="confirmation-items">
                        @foreach($order->items as $item)
                            <div class="summary-line"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong></div>
                        @endforeach
                        <div class="summary-total"><span>Total pesanan</span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
                    </div>
                    @if(!$order->is_demo && $order->payment_status !== 'terverifikasi' && $order->status !== 'dibatalkan' && $order->payment_method !== 'cod')
                        <a class="button button-dark button-wide" href="{{ $whatsappPaymentUrl }}" target="_blank" rel="noopener noreferrer">Minta instruksi & kirim bukti via WhatsApp <span>↗</span></a>
                    @endif
                </section>
            @endif
        </div>
    </section>
@endsection
