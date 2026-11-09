@extends('layouts.app')

@section('title', 'Pesanan '.$order->order_number.' — Al Nusha')

@section('content')
    <section class="section confirmation-section"><div class="container confirmation-card">
        <div class="confirmation-icon">✓</div><span class="eyebrow">PESANAN DITERIMA</span><h1>Terima kasih, pesananmu masuk!</h1><p>Simpan nomor pesanan ini. Minta instruksi pembayaran resmi melalui WhatsApp, lalu kirim bukti pembayaran agar admin dapat memverifikasinya.</p>
        <div class="order-number-box"><span>Nomor pesanan</span><strong>{{ $order->order_number }}</strong><span class="status-pill status-{{ $order->status }}">{{ $orderStatusLabel }}</span></div>
        <div class="payment-notice">Metode: {{ $order->payment_method === 'qris' ? 'QRIS' : 'Transfer bank' }}. Detail QRIS dan rekening masih DEMO/belum dikonfigurasi. Jangan membayar sebelum menerima instruksi resmi dari admin.</div>
        <div class="confirmation-items">@foreach($order->items as $item)<div class="summary-line"><span>{{ $item->product_name }} × {{ $item->quantity }}</span><strong>Rp {{ number_format($item->line_total, 0, ',', '.') }}</strong></div>@endforeach
            <div class="summary-line"><span>Estimasi pengiriman</span><strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong></div><div class="summary-total"><span>Total sementara</span><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></div>
        </div>
        <div class="confirmation-actions"><a class="button button-dark" href="{{ $whatsappPaymentUrl }}" target="_blank" rel="noopener noreferrer">Minta instruksi & kirim bukti via WhatsApp <span>↗</span></a><a class="back-to-shop" href="{{ route('orders.track') }}">Lacak status pesanan</a><a class="back-to-shop" href="{{ route('home') }}">Kembali ke toko</a></div>
    </div></section>
@endsection
