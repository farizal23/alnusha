<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        return view('shop.home', [
            'featuredProducts' => Product::query()->where('is_active', true)->where('is_featured', true)->take(4)->get(),
            'snackCount' => Product::query()->where('is_active', true)->where('category', 'snack')->count(),
            'frozenCount' => Product::query()->where('is_active', true)->where('category', 'frozen')->count(),
        ]);
    }

    public function catalog(Request $request): View
    {
        return $this->catalogView($request);
    }

    public function about(): View
    {
        return view('shop.about');
    }

    public function contact(): View
    {
        return view('shop.contact');
    }

    public function submitPartnership(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'business_name' => ['nullable', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:180'],
            'partnership_type' => ['required', 'in:reseller,supplier,other'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $partnershipTypes = [
            'reseller' => 'Reseller atau distributor',
            'supplier' => 'Penawaran sebagai pemasok',
            'other' => 'Lainnya',
        ];
        $message = implode("\n", [
            'Halo Al Nusha, saya ingin mengajukan kerja sama.',
            '',
            'Nama: '.$validated['name'],
            'Usaha: '.($validated['business_name'] ?: '-'),
            'Nomor WhatsApp: '.$validated['phone'],
            'Email: '.($validated['email'] ?: '-'),
            'Jenis kerja sama: '.$partnershipTypes[$validated['partnership_type']],
            'Detail: '.$validated['message'],
        ]);

        return redirect()->away('https://wa.me/'.config('shop.whatsapp_number').'?text='.rawurlencode($message));
    }

    private function catalogView(Request $request, ?string $category = null): View
    {
        $category = in_array($request->query('kategori'), ['snack', 'frozen'], true)
            ? $request->query('kategori')
            : $category;
        $search = trim((string) $request->query('q', ''));
        $products = Product::query()
            ->where('is_active', true)
            ->when($category !== null, fn ($query) => $query->where('category', $category))
            ->when($search !== '', fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('shop.catalog', [
            'products' => $products,
            'category' => $category,
            'search' => $search,
            'snackCount' => Product::query()->where('is_active', true)->where('category', 'snack')->count(),
            'frozenCount' => Product::query()->where('is_active', true)->where('category', 'frozen')->count(),
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('shop.product', [
            'product' => $product,
            'relatedProducts' => Product::query()
                ->where('is_active', true)
                ->where('category', $product->category)
                ->whereKeyNot($product->id)
                ->take(4)
                ->get(),
        ]);
    }

    public function cart(): View
    {
        return view('shop.cart', $this->cartData());
    }

    public function addToCart(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);
        $cart = $request->session()->get('cart', []);
        $quantity = (int) ($validated['quantity'] ?? 1);
        $newQuantity = (int) ($cart[$product->id] ?? 0) + $quantity;

        if ($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} saat ini tersedia {$product->stock} {$product->unit}.",
            ]);
        }

        $cart[$product->id] = $newQuantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        if ($validated['quantity'] > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Stok {$product->name} tersedia {$product->stock} {$product->unit}.",
            ]);
        }

        $cart = $request->session()->get('cart', []);
        $cart[$product->id] = $validated['quantity'];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Jumlah produk diperbarui.');
    }

    public function removeFromCart(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }

    public function checkout(): View|RedirectResponse
    {
        $cart = $this->cartData();

        if ($cart['items']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja masih kosong.');
        }

        return view('shop.checkout', $cart);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:180'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'customer_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:qris,transfer'],
        ]);
        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja masih kosong.');
        }

        $order = DB::transaction(function () use ($cart, $validated): Order {
            $products = Product::query()
                ->whereIn('id', array_keys($cart))
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($cart)) {
                throw ValidationException::withMessages([
                    'cart' => 'Salah satu produk di keranjang sudah tidak tersedia. Perbarui keranjang sebelum melanjutkan.',
                ]);
            }

            $subtotal = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                if ($quantity < 1 || $quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok {$product->name} berubah. Silakan periksa kembali keranjang Anda.",
                    ]);
                }
                $subtotal += $product->price * $quantity;
            }

            $shippingCost = (int) config('shop.shipping_fee', 12000);
            $order = Order::create([
                ...$validated,
                'order_number' => 'AN-'.now()->format('ymd').'-'.Str::upper(Str::random(8)),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'status' => 'menunggu',
                'payment_status' => 'menunggu',
                'is_demo' => false,
            ]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'line_total' => $product->price * $quantity,
                ]);
                $product->decrement('stock', $quantity);
            }

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order->order_number);
    }

    public function orderConfirmation(Order $order): View
    {
        $order->load('items');

        return view('shop.order-confirmation', [
            'order' => $order,
            'orderStatusLabel' => $this->orderStatusLabel($order),
            'whatsappPaymentUrl' => $this->paymentWhatsAppUrl($order),
        ]);
    }

    public function trackOrder(): View
    {
        return view('shop.track-order', [
            'order' => null,
            'searched' => false,
        ]);
    }

    public function findOrder(Request $request): View
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:32'],
            'customer_phone' => ['required', 'string', 'max:30'],
        ]);
        $order = Order::query()
            ->where('order_number', Str::upper(trim($validated['order_number'])))
            ->where('customer_phone', trim($validated['customer_phone']))
            ->with('items')
            ->first();

        return view('shop.track-order', [
            'order' => $order,
            'searched' => true,
            'orderStatusLabel' => $order ? $this->orderStatusLabel($order) : null,
            'whatsappPaymentUrl' => $order ? $this->paymentWhatsAppUrl($order) : null,
        ]);
    }

    private function orderStatusLabel(Order $order): string
    {
        return match ($order->status) {
            'menunggu' => match ($order->payment_status) {
                'terverifikasi' => 'Menunggu diproses',
                'menunggu' => 'Menunggu pembayaran',
                default => 'Menunggu konfirmasi',
            },
            'diproses' => 'Diproses',
            'dikirim' => 'Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($order->status),
        };
    }

    private function paymentWhatsAppUrl(Order $order): string
    {
        $paymentMethod = $order->payment_method === 'qris' ? 'QRIS' : 'transfer bank';
        $message = "Halo Al Nusha, saya ingin meminta instruksi pembayaran pesanan {$order->order_number} melalui {$paymentMethod}. Total pesanan Rp ".number_format($order->total, 0, ',', '.').'. Saya akan mengirim bukti pembayaran di chat ini setelah membayar.';
        $phone = preg_replace('/\D+/', '', (string) config('shop.whatsapp_number'));

        return 'https://wa.me/'.$phone.'?text='.urlencode($message);
    }

    private function cartData(): array
    {
        $cart = request()->session()->get('cart', []);
        $products = Product::query()->whereIn('id', array_keys($cart))->get()->keyBy('id');
        $items = collect($cart)
            ->map(fn (int $quantity, int|string $id) => $products->has($id) ? [
                'product' => $products->get($id),
                'quantity' => $quantity,
                'line_total' => $products->get($id)->price * $quantity,
            ] : null)
            ->filter()
            ->values();

        return [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
            'shippingCost' => (int) config('shop.shipping_fee', 12000),
        ];
    }
}
