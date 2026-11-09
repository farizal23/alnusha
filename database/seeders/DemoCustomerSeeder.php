<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Data pelanggan DEMO hanya boleh dibuat di lingkungan local atau testing.');
        }

        $customers = [
            [
                'name' => 'Pelanggan Demo A',
                'email' => 'pelanggan.demo.a@example.test',
                'phone' => '000000000001',
                'address' => 'Alamat contoh DEMO A, Bandung',
                'orders' => [
                    [
                        'number' => 'DEMO-AN-0001',
                        'status' => 'selesai',
                        'items' => [['name' => 'Produk Contoh A', 'quantity' => 2, 'price' => 35000]],
                    ],
                    [
                        'number' => 'DEMO-AN-0002',
                        'status' => 'dikirim',
                        'items' => [['name' => 'Produk Contoh B', 'quantity' => 1, 'price' => 49000]],
                    ],
                ],
            ],
            [
                'name' => 'Pelanggan Demo B',
                'email' => 'pelanggan.demo.b@example.test',
                'phone' => '000000000002',
                'address' => 'Alamat contoh DEMO B, Cimahi',
                'orders' => [
                    [
                        'number' => 'DEMO-AN-0003',
                        'status' => 'diproses',
                        'items' => [['name' => 'Produk Contoh C', 'quantity' => 2, 'price' => 25000]],
                    ],
                ],
            ],
            [
                'name' => 'Pelanggan Demo C',
                'email' => 'pelanggan.demo.c@example.test',
                'phone' => '000000000003',
                'address' => 'Alamat contoh DEMO C, Sumedang',
                'orders' => [
                    [
                        'number' => 'DEMO-AN-0004',
                        'status' => 'menunggu',
                        'items' => [['name' => 'Produk Contoh D', 'quantity' => 1, 'price' => 18000]],
                    ],
                ],
            ],
        ];

        foreach ($customers as $customer) {
            foreach ($customer['orders'] as $demoOrder) {
                $subtotal = collect($demoOrder['items'])->sum(
                    fn (array $item): int => $item['quantity'] * $item['price'],
                );
                $shippingCost = 12000;
                $order = Order::updateOrCreate(
                    ['order_number' => $demoOrder['number']],
                    [
                        'customer_name' => $customer['name'],
                        'customer_email' => $customer['email'],
                        'customer_phone' => $customer['phone'],
                        'shipping_address' => $customer['address'],
                        'customer_note' => 'DATA DEMO - bukan pesanan nyata.',
                        'payment_method' => 'transfer',
                        'subtotal' => $subtotal,
                        'shipping_cost' => $shippingCost,
                        'total' => $subtotal + $shippingCost,
                        'status' => $demoOrder['status'],
                        'is_demo' => true,
                    ],
                );

                foreach ($demoOrder['items'] as $item) {
                    $order->items()->updateOrCreate(
                        ['product_name' => $item['name']],
                        [
                            'product_id' => null,
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['price'],
                            'line_total' => $item['quantity'] * $item['price'],
                        ],
                    );
                }
            }
        }
    }
}
