@extends('layouts.app')

@section('title', 'Kontak dan Kerja Sama — Al Nusha')

@section('content')
    <section class="contact-hero">
        <div class="container contact-hero-grid">
            <div class="contact-hero-copy">
                <div class="breadcrumbs"><a href="{{ route('home') }}">Beranda</a><span>/</span><span>Kontak</span></div>
                <span class="eyebrow">KAMI SIAP MEMBANTU</span>
                <h1>Mari terhubung dan <em>bertumbuh bersama.</em></h1>
                <p>Hubungi kami untuk pertanyaan pesanan atau ceritakan peluang kerja sama dengan Al Nusha.</p>
            </div>
            <div class="contact-hero-photo">
                <img src="https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?auto=format&fit=crop&w=1100&q=90" alt="Camilan renyah untuk dinikmati bersama">
                <span class="contact-hero-photo-note">Ada yang bisa kami bantu?</span>
            </div>
        </div>
    </section>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-sidebar">
                    <aside class="contact-info">
                        <span class="eyebrow">HUBUNGI KAMI</span>
                        <h2>Mulai percakapan</h2>
                        <p>Tim Al Nusha siap membantu menjawab pertanyaan produk, pesanan partai besar, dan kerja sama usaha.</p>
                        <a class="contact-info-link" href="tel:{{ config('shop.contact_phone') }}"><span>Telepon</span><strong>{{ config('shop.contact_phone') }}</strong></a>
                        <a class="contact-info-link" href="https://wa.me/{{ config('shop.whatsapp_number') }}"><span>WhatsApp</span><strong>Chat dengan tim kami ↗</strong></a>
                    </aside>
                    <section class="contact-map">
                        <div class="contact-map-heading">
                            <span class="eyebrow">LOKASI</span>
                        </div>
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193097.5058795119!2d107.35810498671876!3d-6.893655899999984!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e64500000001%3A0xd17dad1c712d9495!2sRaja%20CCTV!5e1!3m2!1sid!2sid!4v1794046585925!5m2!1sid!2sid"
                            title="Peta lokasi Raja CCTV"
                            width="600"
                            height="450"
                            style="border:0;"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </section>
                </div>

                <section class="contact-form-card">
                    <span class="eyebrow">PELUANG KERJA SAMA</span>
                    <h2>Ceritakan rencanamu</h2>
                    <p>Isi formulir ini. Rincian pesan akan disiapkan di WhatsApp agar bisa langsung kamu kirim.</p>
                    <form method="POST" action="{{ route('contact.partnership') }}">
                        @csrf
                        <label class="field-label" for="name">Nama lengkap</label>
                        <input class="form-input" id="name" name="name" value="{{ old('name') }}" required maxlength="120" autocomplete="name" placeholder="Nama kamu">
                        @error('name')<span class="contact-error">{{ $message }}</span>@enderror

                        <label class="field-label" for="business_name">Nama usaha <span class="optional-label">Opsional</span></label>
                        <input class="form-input" id="business_name" name="business_name" value="{{ old('business_name') }}" maxlength="160" autocomplete="organization" placeholder="Nama toko atau usaha">
                        @error('business_name')<span class="contact-error">{{ $message }}</span>@enderror

                        <div class="field-row">
                            <div>
                                <label class="field-label" for="phone">Nomor WhatsApp</label>
                                <input class="form-input" id="phone" type="tel" name="phone" value="{{ old('phone') }}" required maxlength="30" autocomplete="tel" placeholder="08xx xxxx xxxx">
                                @error('phone')<span class="contact-error">{{ $message }}</span>@enderror
                            </div>
                            <div>
                                <label class="field-label" for="email">Email <span class="optional-label">Opsional</span></label>
                                <input class="form-input" id="email" type="email" name="email" value="{{ old('email') }}" maxlength="180" autocomplete="email" placeholder="nama@email.com">
                                @error('email')<span class="contact-error">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        <label class="field-label" for="partnership_type">Jenis kerja sama</label>
                        <select class="form-input" id="partnership_type" name="partnership_type" required>
                            <option value="">Pilih jenis kerja sama</option>
                            <option value="reseller" @selected(old('partnership_type') === 'reseller')>Reseller atau distributor</option>
                            <option value="supplier" @selected(old('partnership_type') === 'supplier')>Penawaran sebagai pemasok</option>
                            <option value="other" @selected(old('partnership_type') === 'other')>Lainnya</option>
                        </select>
                        @error('partnership_type')<span class="contact-error">{{ $message }}</span>@enderror

                        <label class="field-label" for="message">Ceritakan kebutuhanmu</label>
                        <textarea class="form-input" id="message" name="message" rows="4" required maxlength="2000" placeholder="Ceritakan rencana atau kebutuhan kerja sama">{{ old('message') }}</textarea>
                        @error('message')<span class="contact-error">{{ $message }}</span>@enderror

                        <button class="button button-dark button-wide" type="submit">Lanjutkan ke WhatsApp <span>↗</span></button>
                    </form>
                </section>
            </div>
        </div>
    </section>
@endsection
