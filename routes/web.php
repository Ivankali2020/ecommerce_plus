<?php

use App\Models\User;
use App\Livewire\Cart;
use App\Models\Seller;
use App\Livewire\Order;
use App\Livewire\Store;
use Illuminate\Support\Str;
use App\Livewire\AllProduct;
use App\Livewire\OrderDetail;
use App\Livewire\ProductDetail;
use App\Livewire\RecentOrders;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Livewire\Seller\SubscriptionPlans;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

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
    // Add this inside your auth middleware group
    Route::get('recent-orders', RecentOrders::class)->name('recent.orders');
});


// Seller subscription routes
Route::middleware(['auth.redirect'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/subscription/plans', SubscriptionPlans::class)->name('subscription.plans');
});


// Add these routes for Google authentication
Route::get('/auth/google', [App\Http\Controllers\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/google/callback', [App\Http\Controllers\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
