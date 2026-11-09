@extends('admin.layout')
@section('title', 'Data pelanggan — Al Nusha')

@section('content')
    <div class="container admin-container">
        <div class="admin-heading">
            <div>
                <span class="eyebrow">PELANGGAN TOKO</span>
                <h1>Data pelanggan</h1>
                <p>Ringkasan pelanggan berdasarkan nomor telepon dan riwayat pesanannya.</p>
            </div>
        </div>
        <section class="admin-panel">
            <div class="admin-panel-heading">
                <div><h2>Daftar pelanggan</h2><p>Data berasal dari informasi yang diberikan saat checkout.</p></div>
                <span class="panel-count">{{ $customers->total() }} pelanggan</span>
            </div>
            <form class="admin-filters" method="GET" action="{{ route('admin.customers.index') }}">
                <input class="form-input" type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau nomor telepon..." aria-label="Cari pelanggan">
                <button class="button button-dark" type="submit">Cari pelanggan</button>
                @if($search !== '')<a class="admin-clear-filter" href="{{ route('admin.customers.index') }}">Hapus pencarian</a>@endif
            </form>
            @if($customers->isEmpty())
                <div class="admin-empty">Belum ada pelanggan yang cocok dengan pencarian.</div>
            @else
                <div class="table-scroll">
                    <table class="admin-table customer-table">
                        <thead><tr><th>Pelanggan</th><th>Email</th><th>Jumlah pesanan</th><th>Nilai pesanan</th><th>Pesanan terakhir</th><th>Riwayat</th></tr></thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>
                                        <strong>{{ $customer->latestOrder->customer_name }}</strong>
                                        @if($customer->is_demo)<span class="demo-badge">DEMO</span>@endif
                                        <small>{{ $customer->customer_phone }}</small>
                                    </td>
                                    <td>{{ $customer->latestOrder->customer_email }}</td>
                                    <td>{{ $customer->orders_count }} pesanan</td>
                                    <td class="table-price">Rp {{ number_format($customer->total_spent, 0, ',', '.') }}</td>
                                    <td><strong>{{ $customer->latestOrder->order_number }}</strong><small><span class="status-pill status-{{ $customer->latestOrder->status }}">{{ ucfirst($customer->latestOrder->status) }}</span></small></td>
                                    <td><a class="text-link" href="{{ route('admin.customers.show', ['phone' => $customer->customer_phone, 'demo' => $customer->is_demo ? 1 : 0]) }}">Lihat riwayat ↗</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="admin-pagination">{{ $customers->links() }}</div>
            @endif
        </section>
    </div>
@endsection
