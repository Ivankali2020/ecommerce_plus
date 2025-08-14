<?php

declare(strict_types=1);

use App\Livewire\ProductDetail;
use App\Livewire\Seller\SubscriptionPlans;
use App\Livewire\Store;
use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Filament\Http\Middleware\Authenticate;
/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Only execute these routes if we're on a tenant domain
$isCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains'));
if (!$isCentralDomain) {

    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
        'assignGlobeScope',
    ])->group(function () {

        // Include authentication routes for tenants
        require __DIR__ . '/auth.php';

        // Public tenant routes - accessible without authentication
        Route::get('/', function () {
            // Tenant-specific landing page
            return view('tenant.welcome', [
                'tenant' => tenant(),
            ]);
        })->name('welcome');

        // Authenticated tenant routes
        Route::middleware(['auth'])->group(function () {
            Route::get('cart', \App\Livewire\Cart::class)->name('cart');
            Route::get('order', \App\Livewire\Order::class)->name('order');
            Route::get('order/{order}', \App\Livewire\OrderDetail::class)->name('order.detail');
            Route::get('recent-orders', \App\Livewire\RecentOrders::class)->name('recent.orders');
            Route::get('gifts', \App\Livewire\GiftPage::class)->name('gifts');
        });

        Route::view('dashboard', 'dashboard')
            ->middleware(['auth']) //, 'verified'
            ->name('dashboard');

        Route::view('profile', 'profile')
            ->middleware(['auth'])
            ->name('profile');


        Route::get('products', \App\Livewire\AllProduct::class)->name('products');
        Route::get('product/{product}', ProductDetail::class)->name('product');
        Route::get('wholesale', \App\Livewire\WholeSale::class)->name('wholesale');

        // Add these routes for Google authentication 
        Route::get('/auth/google', [App\Http\Controllers\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('/google/callback', [App\Http\Controllers\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    });

    Route::get('store/{id}', Store::class)->name('store');



    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
        'assignGlobeScope',
    ])->prefix('seller')->name('seller.')->group(function () {
        // Debug route to test auth (no auth required for debugging)
        Route::get('/debug-auth', function () {
            return response()->json([
                'laravel_seller_guard' => [
                    'authenticated' => auth('seller')->check(),
                    'user' => auth('seller')->user()?->only(['id', 'name', 'email']),
                ],
                'filament_auth' => [
                    'authenticated' => \Filament\Facades\Filament::auth()->check(),
                    'user' => \Filament\Facades\Filament::auth()->user()?->only(['id', 'name', 'email']),
                    'guard' => \Filament\Facades\Filament::getCurrentPanel()?->getAuthGuard(),
                    'current_panel_id' => \Filament\Facades\Filament::getCurrentPanel()?->getId(),
                ],
                'session_data' => [
                    'all_sessions' => array_keys(session()->all()),
                    'seller_login_session' => session()->has('login_seller_' . sha1('seller')),
                ],
                'tenant' => tenant()?->only(['id', 'data']),
                'domain' => request()->getHost(),
                'tenant_scoping_test' => [
                    'total_products_without_scope' => DB::table('products')->count(),
                    'total_products_with_model' => \App\Models\Product::count(),
                    'tenant_id' => tenant('id'),
                ],
            ]);
        })->name('debug-auth');

        // Protected routes - require seller authentication
        Route::middleware([
            'seller.auth', // Use our custom seller auth middleware
        ])->group(function () {
            Route::get('/subscription/plans', SubscriptionPlans::class)->name('subscription.plans');
            // Add POS route for sellers
            Route::get('/pos', \App\Livewire\Seller\Pos::class)->name('pos');

            Route::get('seller/invoice/{record}', \App\Livewire\Invoice::class)->name('invoice');

        });
    });


    Route::get('/filament/exports/{export}/download', DownloadExport::class)
        ->name('filament.exports.download')
        ->middleware(['web', 'auth:seller']);
}
