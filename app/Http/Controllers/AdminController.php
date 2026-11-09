<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Cookie;

class AdminController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    {
        $adminCookie = $request->cookie('admin_session');
        $sessionTokenHash = $request->session()->get('admin_session_token_hash');

        if (
            (bool) $request->session()->get('is_admin', false)
            && is_string($adminCookie)
            && is_string($sessionTokenHash)
            && hash_equals($sessionTokenHash, hash('sha256', $adminCookie))
        ) {
            return redirect()->route('admin.dashboard');
        }

        $request->session()->forget(['is_admin', 'admin_session_token_hash']);

        return view('admin.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);
        $adminEmail = (string) config('shop.admin_email');
        $adminPassword = (string) config('shop.admin_password');

        if ($adminEmail === '' || $adminPassword === '' ||
            ! hash_equals($adminEmail, $credentials['email']) ||
            ! hash_equals($adminPassword, $credentials['password'])) {
            return back()->withErrors(['email' => 'Email atau kata sandi admin tidak cocok.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('is_admin', true);
        $adminToken = Str::random(64);
        $request->session()->put('admin_session_token_hash', hash('sha256', $adminToken));

        return redirect()->intended(route('admin.dashboard'))
            ->withCookie(Cookie::create(
                'admin_session',
                $adminToken,
                0,
                '/admin',
                null,
                $request->isSecure(),
                true,
                false,
                Cookie::SAMESITE_LAX,
            ));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->withCookie(Cookie::create(
                'admin_session',
                '',
                now()->subYear(),
                '/admin',
                null,
                $request->isSecure(),
                true,
                false,
                Cookie::SAMESITE_LAX,
            ));
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'products' => Product::count(),
                'low_stock' => Product::where('stock', '<', 10)->count(),
                'orders' => Order::where('is_demo', false)->count(),
                'pending_orders' => Order::where('is_demo', false)->where('status', 'menunggu')->count(),
                'revenue' => Order::where('is_demo', false)->whereIn('status', ['diproses', 'dikirim', 'selesai'])->sum('total'),
            ],
        ]);
    }

    public function products(Request $request): View
    {
        $category = $request->query('kategori');
        $search = trim((string) $request->query('q', ''));

        return view('admin.products', [
            'products' => Product::query()
                ->when(in_array($category, ['snack', 'frozen'], true), fn ($query) => $query->where('category', $category))
                ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                ->orderBy('category')
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString(),
            'category' => $category,
            'search' => $search,
        ]);
    }

    public function orders(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);
        $search = trim((string) ($validated['q'] ?? ''));

        return view('admin.orders', [
            'orders' => Order::query()
                ->with('items')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                }))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'pendingCount' => Order::where('is_demo', false)->where('status', 'menunggu')->count(),
            'search' => $search,
        ]);
    }

    public function customers(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim((string) ($validated['q'] ?? ''));
        $latestOrderId = Order::query()
            ->from('orders as latest_orders')
            ->select('latest_orders.id')
            ->whereColumn('latest_orders.customer_phone', 'orders.customer_phone')
            ->whereColumn('latest_orders.is_demo', 'orders.is_demo')
            ->orderByDesc('latest_orders.created_at')
            ->orderByDesc('latest_orders.id')
            ->limit(1);
        $customers = Order::query()
            ->select('customer_phone', 'is_demo')
            ->selectSub($latestOrderId, 'latest_order_id')
            ->selectRaw("COUNT(*) as orders_count, SUM(CASE WHEN status != 'dibatalkan' THEN total ELSE 0 END) as total_spent, MAX(created_at) as latest_order_at")
            ->when($search !== '', fn ($query) => $query->whereExists(function ($query) use ($search): void {
                $query->selectRaw('1')
                    ->from('orders as matching_orders')
                    ->whereColumn('matching_orders.customer_phone', 'orders.customer_phone')
                    ->whereColumn('matching_orders.is_demo', 'orders.is_demo')
                    ->where(function ($query) use ($search): void {
                        $query->where('matching_orders.customer_name', 'like', "%{$search}%")
                            ->orWhere('matching_orders.customer_phone', 'like', "%{$search}%");
                    });
            }))
            ->groupBy('customer_phone', 'is_demo')
            ->orderByDesc('latest_order_at')
            ->paginate(15)
            ->withQueryString();
        $latestOrders = Order::query()
            ->whereIn('id', $customers->getCollection()->pluck('latest_order_id'))
            ->get(['id', 'customer_name', 'customer_email', 'customer_phone', 'order_number', 'status', 'created_at'])
            ->keyBy('id');

        $customers->getCollection()->transform(function (object $customer) use ($latestOrders): object {
            $customer->latestOrder = $latestOrders->get($customer->latest_order_id);

            return $customer;
        });

        return view('admin.customers', compact('customers', 'search'));
    }

    public function customer(Request $request, string $phone): View
    {
        $validated = $request->validate([
            'demo' => ['sometimes', 'boolean'],
        ]);
        $isDemo = (bool) ($validated['demo'] ?? false);
        $customerOrders = Order::query()
            ->where('customer_phone', $phone)
            ->where('is_demo', $isDemo);
        $customer = (clone $customerOrders)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->firstOrFail();
        $summary = (clone $customerOrders)
            ->selectRaw("COUNT(*) as orders_count, SUM(CASE WHEN status != 'dibatalkan' THEN total ELSE 0 END) as total_spent")
            ->firstOrFail();
        $orders = $customerOrders
            ->with('items')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.customer', compact('customer', 'isDemo', 'orders', 'summary'));
    }

    public function createProduct(): View
    {
        return view('admin.product-form', ['product' => new Product]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $this->validatedProduct($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);
        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function editProduct(Product $product): View
    {
        return view('admin.product-form', compact('product'));
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validatedProduct($request);
        $validated['slug'] = $this->uniqueSlug($validated['name'], $product->id);
        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Perubahan produk berhasil disimpan.');
    }

    public function deleteProduct(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function updateOrder(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])],
        ]);

        DB::transaction(function () use ($order, $validated): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status === 'dibatalkan' && $validated['status'] !== 'dibatalkan') {
                abort(422, 'Pesanan yang dibatalkan tidak dapat diaktifkan kembali.');
            }
            if (
                in_array($validated['status'], ['diproses', 'dikirim', 'selesai'], true)
                && $lockedOrder->payment_method !== 'cod'
                && $lockedOrder->payment_status !== 'terverifikasi'
            ) {
                throw ValidationException::withMessages([
                    'status' => 'Verifikasi pembayaran terlebih dahulu sebelum memproses pesanan.',
                ]);
            }
            if ($lockedOrder->status !== 'dibatalkan' && $validated['status'] === 'dibatalkan') {
                foreach ($lockedOrder->items as $item) {
                    if ($item->product_id !== null) {
                        Product::query()->whereKey($item->product_id)->increment('stock', $item->quantity);
                    }
                }
            }
            $lockedOrder->update(['status' => $validated['status']]);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan diperbarui.');
    }

    public function verifyOrderPayment(Order $order): RedirectResponse
    {
        DB::transaction(function () use ($order): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);
            if ($lockedOrder->status === 'dibatalkan') {
                throw ValidationException::withMessages([
                    'payment_status' => 'Pembayaran pesanan yang dibatalkan tidak dapat diverifikasi.',
                ]);
            }

            $lockedOrder->update(['payment_status' => 'terverifikasi']);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Pembayaran ditandai terverifikasi.');
    }

    private function validatedProduct(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'category' => ['required', Rule::in(['snack', 'frozen'])],
            'price' => ['required', 'integer', 'min:1', 'max:999999999'],
            'unit' => ['required', 'string', 'max:80'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        return $validated;
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $baseSlug = $slug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
