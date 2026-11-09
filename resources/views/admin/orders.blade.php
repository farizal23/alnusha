@extends('admin.layout')
@section('title', 'Kelola pesanan — Al Nusha')

@section('content')
    <div class="container admin-container">
        <div class="admin-heading"><div><span class="eyebrow">PESANAN PELANGGAN</span><h1>Kelola pesanan</h1><p>{{ $pendingCount }} pesanan menunggu konfirmasi.</p></div></div>
        <section class="admin-panel">
            <div class="admin-panel-heading"><div><h2>Semua pesanan</h2><p>Verifikasi bukti pembayaran dari WhatsApp sebelum mengubah status menjadi Diproses.</p></div><span class="panel-count">{{ $orders->total() }} pesanan</span></div>
            <form class="admin-filters" method="GET" action="{{ route('admin.orders.index') }}">
                <input class="form-input" type="search" name="q" value="{{ $search }}" placeholder="Cari nomor, nama, atau telepon..." aria-label="Cari pesanan">
                <button class="button button-dark" type="submit">Cari pesanan</button>
                @if($search !== '')<a class="admin-clear-filter" href="{{ route('admin.orders.index') }}">Hapus pencarian</a>@endif
            </form>
            @if($orders->isEmpty())<div class="admin-empty">{{ $search !== '' ? 'Belum ada pesanan yang cocok dengan pencarian.' : 'Belum ada pesanan. Pesanan baru dari toko akan tampil di sini.' }}</div>
            @else
                <div class="table-scroll"><table class="admin-table admin-orders-table"><thead><tr><th>Pesanan</th><th>Pelanggan & alamat</th><th>Produk</th><th>Total</th><th>Pembayaran</th><th>Status</th><th>Perbarui</th></tr></thead><tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td id="order-{{ $order->id }}"><strong>{{ $order->order_number }}</strong>@if($order->is_demo)<span class="demo-badge">DEMO</span>@endif<small>{{ $order->created_at->format('d M Y, H:i') }}</small></td>
                            <td><strong>{{ $order->customer_name }}</strong><small>{{ $order->customer_phone }}<br>{{ $order->customer_email }}<br>{{ $order->shipping_address }}</small></td>
                            <td><div class="order-products">@foreach($order->items as $item)<span>{{ $item->product_name }} × {{ $item->quantity }}</span>@endforeach</div></td>
                            <td class="table-price">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td><strong>{{ $order->payment_method === 'qris' ? 'QRIS' : ($order->payment_method === 'cod' ? 'COD (pesanan lama)' : 'Transfer bank') }}</strong><small><span class="status-pill status-{{ $order->payment_status === 'terverifikasi' ? 'selesai' : 'menunggu' }}">{{ $order->payment_status === 'terverifikasi' ? 'Terverifikasi' : 'Menunggu verifikasi' }}</span></small>
                                @if($order->payment_status !== 'terverifikasi' && $order->status !== 'dibatalkan' && $order->payment_method !== 'cod')<form class="payment-verify-form" method="POST" action="{{ route('admin.orders.payment.verify', $order) }}">@csrf @method('PATCH')<button type="submit">Tandai terverifikasi</button></form>@endif
                            </td>
                            <td><span class="status-pill status-{{ $order->status }}">{{ $order->status === 'menunggu' ? ($order->payment_status === 'terverifikasi' ? 'Menunggu diproses' : 'Menunggu pembayaran') : ucfirst($order->status) }}</span></td>
                            <td>@if($order->status !== 'dibatalkan')<form class="status-form" method="POST" action="{{ route('admin.orders.update', $order) }}">@csrf @method('PATCH')<select name="status" aria-label="Status pesanan {{ $order->order_number }}">@foreach(['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Batalkan'] as $value => $label)<option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>@endforeach</select><button type="submit">Simpan</button></form>@else<small>Pesanan ditutup</small>@endif</td>
                        </tr>
                    @endforeach
                </tbody></table></div>
                <div class="admin-pagination">{{ $orders->links() }}</div>
            @endif
        </section>
    </div>
@endsection
