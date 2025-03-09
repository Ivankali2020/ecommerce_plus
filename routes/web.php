<?php

use App\Livewire\Cart;
use App\Livewire\Order;
use App\Livewire\OrderDetail;
use App\Livewire\ProductDetail;
use App\Livewire\Store;
use App\Livewire\AllProduct;
use App\Models\Seller;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    $sellers = Seller::query()
        ->whereHas('media', function($query) {
            $query->where('collection_name', 'logos');
        })
        ->whereHas('addresses')
        ->get();

    return view('welcome', compact('sellers'));
})->name('welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';


Route::get('store/{id}', Store::class)->name('store');
Route::get('product/{product}', ProductDetail::class)->name('product');
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'my'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

Route::middleware(['auth'])->group(function(){
    Route::get('cart', Cart::class)->name('cart');
    Route::get('order', Order::class)->name('order');
    Route::get('order/{order}', OrderDetail::class)->name('order.detail');
    Route::get('products', AllProduct::class)->name('products');
});
