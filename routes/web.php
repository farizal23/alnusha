<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/produk', [ShopController::class, 'catalog'])->name('catalog.index');
Route::redirect('/snack', '/produk?kategori=snack')->name('catalog.snack');
Route::redirect('/frozen-food', '/produk?kategori=frozen')->name('catalog.frozen');
Route::get('/tentang', [ShopController::class, 'about'])->name('about');
Route::get('/kontak', [ShopController::class, 'contact'])->name('contact');
Route::post('/kontak/kerja-sama', [ShopController::class, 'submitPartnership'])->middleware('throttle:5,1')->name('contact.partnership');
Route::get('/produk/{product:slug}', [ShopController::class, 'show'])->name('products.show');
Route::get('/keranjang', [ShopController::class, 'cart'])->name('cart.index');
Route::post('/keranjang/{product}/tambah', [ShopController::class, 'addToCart'])->name('cart.add');
Route::patch('/keranjang/{product}', [ShopController::class, 'updateCart'])->name('cart.update');
Route::delete('/keranjang/{product}', [ShopController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/checkout', [ShopController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [ShopController::class, 'placeOrder'])->name('checkout.store');
Route::get('/pesanan/{order:order_number}', [ShopController::class, 'orderConfirmation'])->name('orders.show');
Route::get('/lacak-pesanan', [ShopController::class, 'trackOrder'])->name('orders.track');
Route::post('/lacak-pesanan', [ShopController::class, 'findOrder'])->middleware('throttle:5,1')->name('orders.find');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/masuk', [AdminController::class, 'login'])->name('login');
    Route::post('/masuk', [AdminController::class, 'authenticate'])->middleware('throttle:5,1')->name('authenticate');
    Route::post('/keluar', [AdminController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/produk', [AdminController::class, 'products'])->name('products.index');
        Route::get('/pesanan', [AdminController::class, 'orders'])->name('orders.index');
        Route::patch('/pesanan/{order}/pembayaran', [AdminController::class, 'verifyOrderPayment'])->name('orders.payment.verify');
        Route::get('/pelanggan', [AdminController::class, 'customers'])->name('customers.index');
        Route::get('/pelanggan/{phone}', [AdminController::class, 'customer'])->name('customers.show');
        Route::get('/produk/baru', [AdminController::class, 'createProduct'])->name('products.create');
        Route::post('/produk', [AdminController::class, 'storeProduct'])->name('products.store');
        Route::get('/produk/{product}/ubah', [AdminController::class, 'editProduct'])->name('products.edit');
        Route::put('/produk/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
        Route::delete('/produk/{product}', [AdminController::class, 'deleteProduct'])->name('products.destroy');
        Route::patch('/pesanan/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
    });
});
