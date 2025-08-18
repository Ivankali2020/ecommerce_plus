<?php

declare(strict_types=1);

use App\Livewire\ProductDetail;
use App\Livewire\Seller\SubscriptionPlans;
use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Telegram\Bot\Api;
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
if (! $isCentralDomain) {

    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
        'assignGlobeScope',
    ])->group(function () {

        // Include authentication routes for tenants
        require __DIR__.'/auth.php';

        // Public tenant routes - accessible without authentication
        Route::get('/', function () {
            // Tenant-specific landing page
            return view('tenant.welcome', [
                'tenant' => tenant(),
            ]);
        })->name('welcome');

        // Authenticated tenant routes
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('cart', \App\Livewire\Cart::class)->name('cart');
            Route::get('order', \App\Livewire\Order::class)->name('order');
            Route::get('order/{order}', \App\Livewire\OrderDetail::class)->name('order.detail');
            Route::get('recent-orders', \App\Livewire\RecentOrders::class)->name('recent.orders');
            Route::get('gifts', \App\Livewire\GiftPage::class)->name('gifts');
            Route::get('gift-exchange-history', \App\Livewire\GiftExchangeHistory::class)->name('gift.exchange.history');
            Route::get('point-transaction-history', \App\Livewire\PointTransactionHistory::class)->name('point.transaction.history');
            Route::view('dashboard', 'dashboard')->name('dashboard');
            Route::view('profile', 'profile')->name('profile');
        });

        Route::get('products', \App\Livewire\AllProduct::class)->name('products');
        Route::get('product/{product}', ProductDetail::class)->name('product');
        Route::get('wholesale', \App\Livewire\WholeSale::class)->name('wholesale');
        Route::get('delivery', \App\Livewire\DeliveryFees::class)->name('delivery');

        // Content Pages Routes (Public Access)
        Route::get('privacy-policy', \App\Livewire\Content\PrivacyPolicy::class)->name('privacy-policy');
        Route::get('terms-conditions', \App\Livewire\Content\TermsConditions::class)->name('terms-conditions');
        Route::get('about-us', \App\Livewire\Content\AboutUs::class)->name('about-us');
        Route::get('returns-exchanges', \App\Livewire\Content\ReturnsExchanges::class)->name('returns-exchanges');
        Route::get('faq', \App\Livewire\Content\Faq::class)->name('faq');

        // Add these routes for Google authentication
        Route::get('/auth/google', [App\Http\Controllers\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
        Route::get('/google/callback', [App\Http\Controllers\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    });

    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
        'assignGlobeScope',
    ])->prefix('seller')->name('seller.')->group(function () {
        // Debug route to test auth (no auth required for debugging)
        Route::get('/debug-auth', function () {

            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            
            // Use your personal chat ID
            $channelId = env('TELEGRAM_CHANNEL_ID', '1825168720'); // Your personal chat ID
            
            $telegram->sendMessage([
                'chat_id' => $channelId,
                'text' => 'Debug Auth Route Accessed from: ' . request()->getHost() . "\nTime: " . now()->format('Y-m-d H:i:s'),
                'parse_mode' => 'Markdown',
            ]);
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
                    'seller_login_session' => session()->has('login_seller_'.sha1('seller')),
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

        // Route to get seller's Telegram chat ID from bot
        Route::get('/get-telegram-updates', function () {
            try {
                $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
                $updates = $telegram->getUpdates(['limit' => 10]);
                
                $chatIds = collect($updates)->map(function ($update) {
                    $message = $update['message'] ?? null;
                    if (!$message) return null;
                    
                    return [
                        'chat_id' => $message['chat']['id'],
                        'first_name' => $message['from']['first_name'] ?? '',
                        'last_name' => $message['from']['last_name'] ?? '',
                        'username' => $message['from']['username'] ?? '',
                        'text' => $message['text'] ?? '',
                        'date' => date('Y-m-d H:i:s', $message['date']),
                    ];
                })->filter()->unique('chat_id')->values();
                
                return response()->json([
                    'success' => true,
                    'chat_ids' => $chatIds,
                    'instructions' => [
                        '1. Send a message to your bot (@ivannoti_bot) first',
                        '2. Look for your chat_id in the response above',
                        '3. Copy the chat_id and update your seller profile',
                        '4. You will then receive order notifications'
                    ]
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
        })->name('get-telegram-updates');

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
