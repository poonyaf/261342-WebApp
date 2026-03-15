<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AdminController;
//for call pic from storage
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [ProductController::class, 'index'])->name('products.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/product-photo/{filename}', [ProductController::class, 'showPhoto'])->name('product.photo');
//call picture
Route::get('/product-photo/{filename}', function ($filename) {
    // กำหนด path ที่เก็บรูปสินค้า (ตรวจสอบว่าในเครื่องเก็บไว้ที่ไหน)
    $path = 'products/' . $filename;

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->get($path);
    $type = Storage::disk('public')->mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
})->name('product.photo');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo/update', [UserController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::get('/profile/photo/{filename}', [UserController::class, 'showProfilePhoto'])->where('filename', '.*')->name('user.photo'); //to read
   
    
     // Cart
    Route::get('/carts', [CartController::class, 'index'])->name('carts.index');
    Route::post('/carts', [CartController::class, 'store'])->name('carts.store');
    Route::patch('/carts/{id}', [CartController::class, 'update'])->name('carts.update');
    Route::delete('/carts/{id}', [CartController::class, 'destroy'])->name('carts.destroy');

    // Orders
    Route::get('/orders/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/confirm', [OrderController::class, 'store'])->name('orders.confirm.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/now', [OrderController::class, 'storeNow'])->name('orders.storeNow');
    Route::patch('orders/{id}/pay', [OrderController::class, 'markAsPaid'])->name('orders.pay');
    Route::patch('orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::patch('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');

  // Payments
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('/payments/create/{order_id}', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
//wishlist
Route::get('/wishlists', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlists', [WishlistController::class, 'store'])->name('wishlist.store');
//when user click the wishlist button, it will toggle the wishlist status of the product
Route::post('/wishlists/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlists/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
    
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/users/{id}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::post('/admin/users/add-admin', [AdminController::class, 'storeAdmin'])->name('admin.users.storeAdmin');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::delete('/products/{id}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('/orders/{id}/packing', [AdminController::class, 'markAsPacking'])->name('admin.orders.packing');
    Route::patch('/orders/{id}/delivering', [AdminController::class, 'markAsDelivering'])->name('admin.orders.delivering');
    Route::patch('/orders/{id}/complete', [AdminController::class, 'markAsComplete'])->name('admin.orders.complete');
    Route::patch('/orders/{id}/processing', [AdminController::class, 'markAsProcessing'])->name('admin.orders.processing');
    Route::patch('/orders/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.orders.updateStatus');
});
require __DIR__.'/auth.php';
