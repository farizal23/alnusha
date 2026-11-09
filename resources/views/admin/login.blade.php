<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin masuk — Al Nusha</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body class="login-page" style="--page-background-image: url('{{ asset('images/latar_belakang.jpg') }}')"><a class="login-back" href="{{ route('home') }}">← Kembali ke toko</a>
    <main class="login-card">
        <a class="brand login-brand" href="{{ route('home') }}"><span class="brand-mark">an<span>.</span></span><span class="brand-copy"><strong>al nusha</strong><small>ADMIN STUDIO</small></span></a>
        <span class="eyebrow">SELAMAT DATANG KEMBALI</span><h1>Masuk ke dashboard.</h1><p>Kelola produk, stok, dan pesanan Al Nusha di satu tempat.</p>
        @if($errors->any())<div class="flash flash-error">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.authenticate') }}">@csrf
            <label class="field-label" for="email">Email admin</label><input class="form-input" type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" autofocus placeholder="admin@alnusha.id">
            <label class="field-label" for="password">Kata sandi</label><input class="form-input" type="password" name="password" id="password" required autocomplete="current-password" placeholder="Masukkan kata sandi">
            <button class="button button-dark button-wide" type="submit">Masuk dashboard <span>↗</span></button>
        </form>
        <small class="login-footnote">Akses khusus pengelola toko.</small>
    </main>
</body>
</html>
