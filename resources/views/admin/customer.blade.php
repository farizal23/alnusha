@extends('admin.layout')
@section('title', 'Riwayat pelanggan — Al Nusha')

@section('content')
    <div class="container admin-container">
        <div class="admin-heading">
            <div>
                <span class="eyebrow">RIWAYAT PELANGGAN</span>
                <h1>{{ $customer->customer_name }} @if($isDemo)<span class="demo-badge">DEMO</span>@endif</h1>
                <p>{{ $customer->customer_phone }} · {{ $customer->customer_email }}</p>
            </div>
            <a class="button button-light" href="{{ route('admin.customers.index') }}">← Kembali ke pelanggan</a>
        </div>
        <div class="customer-summary">
            <article class="stat-card"><span>Jumlah pesanan</span><strong>{{ $summary->orders_count }}</strong><small>Semua status pesanan</small></article>
            <article class="stat-card"><span>Nilai pesanan</span><strong class="stat-revenue">Rp {{ number_format($summary->total_spent, 0, ',', '.') }}</strong><small>Tidak termasuk pesanan dibatalkan</small></article>
            <article class="stat-card"><span>Status terakhir</span><strong class="customer-status"><span class="status-pill status-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span></strong><small>{{ $customer->order_number }}</small></article>
        </div>
        <section class="admin-panel">
            <div class="admin-panel-heading">
                <div><h2>Riwayat pesanan</h2><p>Informasi pengiriman dan produk yang dicatat untuk setiap pesanan.</p></div>
                <span class="panel-count">{{ $orders->total() }} pesanan</span>
            </div>
            <div class="table-scroll">
                <table class="admin-table customer-history-table">
                    <thead><tr><th>Pesanan</th><th>Produk</th><th>Total & pembayaran</th><th>Alamat & catatan</th><th>Status</th><th>Kelola</th></tr></thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong>@if($order->is_demo)<span class="demo-badge">DEMO</span>@endif<small>{{ $order->created_at->format('d M Y, H:i') }}</small></td>
                                <td><div class="order-products">@foreach($order->items as $item)<span>{{ $item->product_name }} × {{ $item->quantity }}</span>@endforeach</div></td>
                                <td class="table-price">Rp {{ number_format($order->total, 0, ',', '.') }}<small>{{ $order->payment_method === 'cod' ? 'Bayar saat diterima' : 'Transfer bank' }}</small></td>
                                <td>{{ $order->shipping_address }}@if($order->customer_note)<small>Catatan: {{ $order->customer_note }}</small>@endif</td>
                                <td><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                                <td><a class="text-link" href="{{ route('admin.orders.index', ['q' => $order->order_number]) }}">Buka pesanan ↗</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="admin-pagination">{{ $orders->links() }}</div>
        </section>
    </div>
@endsection
