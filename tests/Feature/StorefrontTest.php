<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_and_product_catalog_are_separate_and_category_filters_share_one_page(): void
    {
        Product::create([
            'name' => 'Kriwil pedas',
            'slug' => 'kriwil-pedas',
            'category' => 'snack',
            'price' => 73000,
            'unit' => '1 ball',
            'stock' => 10,
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Tahu baso',
            'slug' => 'tahu-baso',
            'category' => 'frozen',
            'price' => 25000,
            'unit' => 'per pack',
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Camilan baik')
            ->assertSee('>Produk</a>', false)
            ->assertSee('aria-controls="main-nav"', false)
            ->assertSee('data-nav-toggle', false)
            ->assertSee('wa.me/6281222720099')
            ->assertDontSee('floating-whatsapp')
            ->assertDontSee('>Koleksi</a>', false)
            ->assertDontSee('collection-section');

        $this->get(route('catalog.index', ['q' => 'Kriwil']))
            ->assertOk()
            ->assertSee('hero-grid')
            ->assertSee('photo-1563245372-f21724e3856d')
            ->assertSee('Lihat produk')
            ->assertSee('Kriwil pedas')
            ->assertDontSee('Tahu baso');

        $this->get(route('catalog.index', ['kategori' => 'snack']))
            ->assertOk()
            ->assertSee('Kriwil pedas')
            ->assertDontSee('Tahu baso');

        $this->get(route('catalog.index', ['kategori' => 'frozen']))
            ->assertOk()
            ->assertSee('Tahu baso')
            ->assertDontSee('Kriwil pedas');

        $this->get(route('catalog.snack'))->assertRedirect(route('catalog.index', ['kategori' => 'snack']));
        $this->get(route('catalog.frozen'))->assertRedirect(route('catalog.index', ['kategori' => 'frozen']));

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('Tentang kami')
            ->assertDontSee('collection-section');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Ceritakan rencanamu')
            ->assertSee('081222720099')
            ->assertDontSee('floating-whatsapp')
            ->assertSee('Peta lokasi Raja CCTV')
            ->assertSee('Instagram')
            ->assertSee('Facebook')
            ->assertSee('TikTok');
    }

    public function test_partnership_form_redirects_to_whatsapp_with_validated_details(): void
    {
        $this->post(route('contact.partnership'), [
            'name' => 'Nusa Pelanggan',
            'business_name' => 'Toko Nusa',
            'phone' => '081234567890',
            'email' => 'nusa@example.com',
            'partnership_type' => 'reseller',
            'message' => 'Saya ingin menjadi reseller.',
        ])->assertRedirect('https://wa.me/6281222720099?text='.rawurlencode(
            "Halo Al Nusha, saya ingin mengajukan kerja sama.\n\nNama: Nusa Pelanggan\nUsaha: Toko Nusa\nNomor WhatsApp: 081234567890\nEmail: nusa@example.com\nJenis kerja sama: Reseller atau distributor\nDetail: Saya ingin menjadi reseller."
        ));
    }

    public function test_partnership_form_rejects_invalid_contact_details(): void
    {
        $this->from(route('contact'))
            ->post(route('contact.partnership'), [
                'name' => '',
                'phone' => '',
                'email' => 'not-an-email',
                'partnership_type' => 'unknown',
                'message' => '',
            ])
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'phone', 'email', 'partnership_type', 'message']);
    }

    public function test_checkout_records_order_and_decrements_inventory(): void
    {
        config(['shop.shipping_fee' => 12000]);
        $product = Product::create([
            'name' => 'Tahu baso',
            'slug' => 'tahu-baso',
            'category' => 'frozen',
            'price' => 25000,
            'unit' => 'per pack',
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Nusa Pelanggan',
                'customer_email' => 'buyer@example.com',
                'customer_phone' => '081234567890',
                'shipping_address' => 'Jl. Contoh No. 1, Bandung',
                'payment_method' => 'transfer',
            ])
            ->assertRedirect();

        $order = Order::with('items')->firstOrFail();
        $this->assertSame(62000, $order->total);
        $this->assertSame('Tahu baso', $order->items->first()->product_name);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertDatabaseHas('orders', [
            'order_number' => $order->order_number,
            'status' => 'menunggu',
            'payment_status' => 'menunggu',
        ]);
    }

    public function test_checkout_rejects_an_order_above_available_stock(): void
    {
        $product = Product::create([
            'name' => 'Basreng',
            'slug' => 'basreng',
            'category' => 'snack',
            'price' => 74000,
            'unit' => '1 ball',
            'stock' => 1,
            'is_active' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Nusa Pelanggan',
                'customer_email' => 'buyer@example.com',
                'customer_phone' => '081234567890',
                'shipping_address' => 'Jl. Contoh No. 1, Bandung',
                'payment_method' => 'transfer',
            ])
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
    }

    public function test_checkout_offers_qris_and_bank_transfer_with_demo_payment_warning(): void
    {
        $product = Product::create([
            'name' => 'Basreng',
            'slug' => 'basreng',
            'category' => 'snack',
            'price' => 74000,
            'unit' => '1 ball',
            'stock' => 1,
            'is_active' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('QRIS')
            ->assertSee('Transfer bank')
            ->assertSee('value="qris" checked', false)
            ->assertSee('belum ditambahkan')
            ->assertDontSee('Bayar saat pesanan diterima');
    }

    public function test_checkout_rejects_cash_on_delivery(): void
    {
        $product = Product::create([
            'name' => 'Basreng',
            'slug' => 'basreng',
            'category' => 'snack',
            'price' => 74000,
            'unit' => '1 ball',
            'stock' => 1,
            'is_active' => true,
        ]);

        $this->withSession(['cart' => [$product->id => 1]])
            ->post(route('checkout.store'), [
                'customer_name' => 'Nusa Pelanggan',
                'customer_email' => 'buyer@example.com',
                'customer_phone' => '081234567890',
                'shipping_address' => 'Jl. Contoh No. 1, Bandung',
                'payment_method' => 'cod',
            ])
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_customer_can_track_an_order_with_its_number_and_checkout_phone(): void
    {
        $order = $this->makeOrder('dikirim', 'terverifikasi');
        $order->update(['shipping_address' => 'Alamat rahasia pelanggan']);
        $order->items()->create([
            'product_name' => 'Basreng',
            'quantity' => 2,
            'unit_price' => 74000,
            'line_total' => 148000,
        ]);

        $this->post(route('orders.find'), [
            'order_number' => strtolower($order->order_number),
            'customer_phone' => '081234567890',
        ])
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Dikirim')
            ->assertSee('Terverifikasi')
            ->assertSee('Basreng')
            ->assertDontSee('Alamat rahasia pelanggan')
            ->assertDontSee('081234567890');

        $this->post(route('orders.find'), [
            'order_number' => $order->order_number,
            'customer_phone' => '081111111111',
        ])
            ->assertOk()
            ->assertSee('Pesanan tidak ditemukan')
            ->assertDontSee($order->order_number);
    }

    public function test_customer_can_track_demo_order_and_it_is_clearly_marked_as_fictional(): void
    {
        $order = $this->makeOrder();
        $order->update(['is_demo' => true]);
        $order->items()->create([
            'product_name' => 'Produk Contoh',
            'quantity' => 1,
            'unit_price' => 74000,
            'line_total' => 74000,
        ]);

        $this->post(route('orders.find'), [
            'order_number' => $order->order_number,
            'customer_phone' => '081234567890',
        ])
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('DEMO · DATA CONTOH')
            ->assertSee('tidak ada pembayaran atau pengiriman sungguhan')
            ->assertSee('Produk Contoh')
            ->assertDontSee('Minta instruksi &amp; kirim bukti via WhatsApp');
    }

    public function test_admin_must_verify_transfer_payment_before_processing_order(): void
    {
        $order = $this->makeOrder();

        $this->authenticateAdmin()
            ->patch(route('admin.orders.update', $order), ['status' => 'diproses'])
            ->assertSessionHasErrors('status');

        $this->patch(route('admin.orders.payment.verify', $order))
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'terverifikasi',
            'status' => 'menunggu',
        ]);

        $this->patch(route('admin.orders.update', $order), ['status' => 'diproses'])
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'diproses']);
    }

    public function test_payment_verification_requires_an_admin_session(): void
    {
        $order = $this->makeOrder();

        $this->patch(route('admin.orders.payment.verify', $order))
            ->assertRedirect(route('admin.login'));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_status' => 'menunggu']);
    }

    public function test_admin_dashboard_requires_an_admin_session(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_sign_in_and_view_the_dashboard(): void
    {
        config([
            'shop.admin_email' => 'admin@example.com',
            'shop.admin_password' => 'a-strong-test-password',
        ]);

        $this->post(route('admin.authenticate'), [
            'email' => 'admin@example.com',
            'password' => 'a-strong-test-password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->authenticateAdmin();
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('RINGKASAN TOKO');
    }

    public function test_admin_access_requires_the_session_cookie_that_expires_when_the_browser_closes(): void
    {
        $response = $this->withSession([
            'is_admin' => true,
            'admin_session_token_hash' => hash('sha256', 'browser-session-token'),
        ])
            ->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'))
            ->assertSessionMissing('is_admin');
    }

    public function test_admin_login_page_does_not_restore_authentication_without_browser_cookie(): void
    {
        $response = $this->withSession([
            'is_admin' => true,
            'admin_session_token_hash' => hash('sha256', 'browser-session-token'),
        ])->get(route('admin.login'));

        $response->assertOk()
            ->assertSee('Masuk ke dashboard.')
            ->assertSessionMissing('is_admin');
    }

    public function test_admin_login_sets_a_http_only_browser_session_cookie_for_admin_pages(): void
    {
        config([
            'shop.admin_email' => 'admin@example.com',
            'shop.admin_password' => 'a-strong-test-password',
        ]);

        $response = $this->post(route('admin.authenticate'), [
            'email' => 'admin@example.com',
            'password' => 'a-strong-test-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $adminCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === 'admin_session');

        $this->assertNotNull($adminCookie);
        $this->assertSame(0, $adminCookie->getExpiresTime());
        $this->assertSame('/admin', $adminCookie->getPath());
        $this->assertTrue($adminCookie->isHttpOnly());
    }

    public function test_admin_logout_expires_its_browser_session_cookie(): void
    {
        $response = $this->authenticateAdmin()
            ->post(route('admin.logout'));
        $response->assertRedirect(route('home'));

        $adminCookie = collect($response->headers->getCookies())
            ->first(fn ($cookie): bool => $cookie->getName() === 'admin_session');

        $this->assertNotNull($adminCookie);
        $this->assertLessThan(time(), $adminCookie->getExpiresTime());
    }

    public function test_cancelling_an_order_restores_reserved_stock(): void
    {
        $product = Product::create([
            'name' => 'Pilus sarinah',
            'slug' => 'pilus-sarinah',
            'category' => 'snack',
            'price' => 48000,
            'unit' => '1 ball',
            'stock' => 3,
            'is_active' => true,
        ]);
        $order = Order::create([
            'order_number' => 'AN-261107-TEST0001',
            'customer_name' => 'Nusa Pelanggan',
            'customer_email' => 'buyer@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Contoh No. 1, Bandung',
            'payment_method' => 'transfer',
            'subtotal' => 96000,
            'shipping_cost' => 12000,
            'total' => 108000,
            'status' => 'menunggu',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => $product->price,
            'line_total' => 96000,
        ]);

        $this->authenticateAdmin()
            ->patch(route('admin.orders.update', $order), ['status' => 'dibatalkan'])
            ->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'dibatalkan']);
    }

    public function test_admin_product_and_order_management_use_separate_pages(): void
    {
        Product::create([
            'name' => 'Kriwil pedas',
            'slug' => 'kriwil-pedas',
            'category' => 'snack',
            'price' => 73000,
            'unit' => '1 ball',
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->authenticateAdmin()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan')
            ->assertDontSee('Daftar produk')
            ->assertDontSee('Semua pesanan');

        $this->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Daftar produk')
            ->assertSee('Kriwil pedas');

        $this->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Semua pesanan')
            ->assertDontSee('Kriwil pedas');
    }

    private function makeOrder(
        string $status = 'menunggu',
        string $paymentStatus = 'menunggu',
        string $paymentMethod = 'transfer',
    ): Order {
        return Order::create([
            'order_number' => 'AN-261108-'.strtoupper(fake()->bothify('????????')),
            'customer_name' => 'Nusa Pelanggan',
            'customer_email' => 'buyer@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Contoh No. 1, Bandung',
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'subtotal' => 74000,
            'shipping_cost' => 12000,
            'total' => 86000,
            'status' => $status,
        ]);
    }

    private function authenticateAdmin(): static
    {
        $token = 'test-admin-session-token';

        return $this->withSession([
            'is_admin' => true,
            'admin_session_token_hash' => hash('sha256', $token),
        ])->withCookie('admin_session', $token);
    }
}
