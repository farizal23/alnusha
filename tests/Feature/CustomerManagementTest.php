<?php

namespace Tests\Feature;

use App\Models\Order;
use Database\Seeders\DemoCustomerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use RuntimeException;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_pages_require_an_admin_session(): void
    {
        $this->get(route('admin.customers.index'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('admin.customers.show', ['phone' => '081234567890']))
            ->assertRedirect(route('admin.login'));
    }

    public function test_customer_list_groups_orders_by_phone_and_searches_name_or_phone(): void
    {
        $this->createOrder('AN-REAL-0001', 'Dewi Lama', '081234567890', 80000, 'selesai', false, now()->subDays(2));
        $this->createOrder('AN-REAL-0002', "Dewi <script>alert('x')</script>", '081234567890', 60000, 'dikirim', false, now()->subDay());
        $this->createOrder('DEMO-AN-0001', 'Pelanggan Demo A', '081234567890', 900000, 'menunggu', true);
        $this->createOrder('AN-REAL-0003', 'Nadia Cari', '081111111111', 45000, 'menunggu');

        $this->authenticateAdmin()
            ->get(route('admin.customers.index', ['q' => '081234567890']))
            ->assertOk()
            ->assertSee('Dewi &lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;', false)
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertDontSee('Dewi Lama')
            ->assertSee('2 pesanan')
            ->assertSee('Rp 140.000')
            ->assertSee('Pelanggan Demo A')
            ->assertSee('DEMO')
            ->assertSee('Rp 900.000');

        $this->get(route('admin.customers.index', ['q' => 'Nadia']))
            ->assertOk()
            ->assertSee('Nadia Cari')
            ->assertDontSee('Pelanggan Demo A')
            ->assertDontSee('Dewi Lama');

        $this->get(route('admin.customers.index', ['q' => 'Dewi Lama']))
            ->assertOk()
            ->assertSee('2 pesanan')
            ->assertSee('Rp 140.000')
            ->assertDontSee('Nadia Cari');
    }

    public function test_customer_history_shows_only_the_selected_real_or_demo_customer(): void
    {
        $this->createOrder('AN-REAL-0001', 'Pelanggan Nyata', '081234567890', 80000, 'dibatalkan', false, now()->subDays(2));
        $latestOrder = $this->createOrder('AN-REAL-0002', 'Pelanggan Nyata', '081234567890', 60000, 'dikirim', false, now()->subDay());
        $latestOrder->items()->create([
            'product_name' => 'Tahu baso',
            'quantity' => 2,
            'unit_price' => 24000,
            'line_total' => 48000,
        ]);
        $demoOrder = $this->createOrder('DEMO-AN-0001', 'Pelanggan Demo A', '081234567890', 900000, 'selesai', true);
        $demoOrder->update(['customer_note' => 'DATA DEMO - bukan pesanan nyata.']);

        $this->authenticateAdmin()
            ->get(route('admin.customers.show', ['phone' => '081234567890']))
            ->assertOk()
            ->assertSee('Pelanggan Nyata')
            ->assertSee('2 pesanan')
            ->assertSee('Rp 60.000')
            ->assertSee('AN-REAL-0001')
            ->assertSee('AN-REAL-0002')
            ->assertSee('Tahu baso')
            ->assertDontSee('DEMO-AN-0001')
            ->assertDontSee('Pelanggan Demo A')
            ->assertSee(route('admin.orders.index', ['q' => 'AN-REAL-0002']));

        $this->get(route('admin.orders.index', ['q' => 'AN-REAL-0002']))
            ->assertOk()
            ->assertSee('AN-REAL-0002')
            ->assertDontSee('AN-REAL-0001');

        $this->get(route('admin.customers.show', ['phone' => '081234567890', 'demo' => 1]))
            ->assertOk()
            ->assertSee('Pelanggan Demo A')
            ->assertSee('DEMO-AN-0001')
            ->assertSee('DATA DEMO');
    }

    public function test_customer_detail_returns_not_found_for_an_unknown_phone(): void
    {
        $this->authenticateAdmin()
            ->get(route('admin.customers.show', ['phone' => '080000000099']))
            ->assertNotFound();
    }

    public function test_demo_orders_do_not_change_real_order_dashboard_totals(): void
    {
        $this->createOrder('AN-REAL-0001', 'Pelanggan Nyata', '081234567890', 50000, 'diproses');
        $this->createOrder('DEMO-AN-0001', 'Pelanggan Demo A', '080000000001', 900000, 'selesai', true);

        $this->authenticateAdmin()
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-stat-value="orders">1</strong>', false)
            ->assertSee('Rp 50.000')
            ->assertDontSee('Rp 900.000');
    }

    public function test_demo_seeder_is_repeatable_and_keeps_real_orders_separate(): void
    {
        $this->createOrder('AN-REAL-0001', 'Pelanggan Nyata', '081234567890', 50000);

        $this->seed(DemoCustomerSeeder::class);
        $this->seed(DemoCustomerSeeder::class);

        $this->assertDatabaseCount('orders', 5);
        $this->assertDatabaseCount('order_items', 4);
        $this->assertDatabaseHas('orders', [
            'order_number' => 'AN-REAL-0001',
            'is_demo' => false,
        ]);
        $this->assertDatabaseHas('orders', [
            'order_number' => 'DEMO-AN-0001',
            'is_demo' => true,
            'customer_phone' => '000000000001',
        ]);
    }

    public function test_demo_seeder_refuses_to_write_outside_local_or_testing(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Data pelanggan DEMO hanya boleh dibuat di lingkungan local atau testing.');
            app(DemoCustomerSeeder::class)->run();
        } finally {
            $this->app->detectEnvironment(fn (): string => 'testing');
        }
    }

    private function createOrder(
        string $orderNumber,
        string $customerName,
        string $customerPhone,
        int $total,
        string $status = 'menunggu',
        bool $isDemo = false,
        ?Carbon $createdAt = null,
    ): Order {
        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_name' => $customerName,
            'customer_email' => 'customer@example.test',
            'customer_phone' => $customerPhone,
            'shipping_address' => 'Alamat contoh, Bandung',
            'customer_note' => null,
            'payment_method' => 'transfer',
            'subtotal' => $total,
            'shipping_cost' => 0,
            'total' => $total,
            'status' => $status,
            'is_demo' => $isDemo,
        ]);

        if ($createdAt !== null) {
            $order->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }

        return $order;
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
