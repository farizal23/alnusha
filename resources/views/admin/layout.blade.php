<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#183c35">
    <title>@yield('title', 'Admin Al Nusha')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body" style="--page-background-image: url('{{ asset('images/latar_belakang.jpg') }}')">
    <header class="admin-topbar"><div class="container admin-topbar-inner">
        <a class="brand" href="{{ route('admin.dashboard') }}"><span class="brand-mark">an<span>.</span></span><span class="brand-copy"><strong>al nusha</strong><small>ADMIN STUDIO</small></span></a>
        <div class="admin-top-actions"><a href="{{ route('home') }}">Lihat toko ↗</a><form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit">Keluar</button></form></div>
    </div></header>
    <nav class="admin-nav"><div class="container admin-nav-inner">
        <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Ringkasan</a>
        <a href="{{ route('admin.products.index') }}" @class(['active' => request()->routeIs('admin.products.*')])>Produk</a>
        <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>Pesanan</a>
        <a href="{{ route('admin.customers.index') }}" @class(['active' => request()->routeIs('admin.customers.*')])>Data Pelanggan</a>
    </div></nav>
    @if(session('success') || $errors->any())
        <div class="container flash-wrap">@if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif @if($errors->any())<div class="flash flash-error">{{ $errors->first() }}</div>@endif</div>
    @endif
    <main class="admin-main">@yield('content')</main>
</body>
</html>
