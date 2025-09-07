<?php

use App\Http\Controllers\ProductImportExportController;
use App\Livewire\Profile\UserAddressForm;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;


// Check if the current domain is a central domain or a tenant domain
$isCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains'));

// Only include auth.php for central domains
if ($isCentralDomain) {
    require __DIR__ . '/auth.php';
    // Telegram webhook routes (global for all tenants)
    Route::post('/telegram/webhook', [App\Http\Controllers\TelegramWebhookController::class, 'webhook'])
        ->name('telegram.webhook');
    
    Route::get('/telegram/set-webhook', [App\Http\Controllers\TelegramWebhookController::class, 'setWebhook'])
        ->name('telegram.set-webhook');
    
    Route::get('/telegram/webhook-info', [App\Http\Controllers\TelegramWebhookController::class, 'getWebhookInfo'])
        ->name('telegram.webhook-info');
        
    // Manual testing for local development
    Route::get('/telegram/test-command/{command}', function ($command) {
        $telegramService = new \App\Services\TelegramCommandService();
        
        $fakeUpdate = new \Telegram\Bot\Objects\Update([
            'update_id' => 999999,
            'message' => [
                'message_id' => 999,
                'from' => [
                    'id' => 1825168720, // Your chat ID
                    'is_bot' => false,
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'username' => 'testuser'
                ],
                'chat' => [
                    'id' => 1825168720,
                    'type' => 'private'
                ],
                'date' => time(),
                'text' => '/' . $command
            ]
        ]);
        
        $telegramService->handleUpdate($fakeUpdate);
        
        return response()->json([
            'success' => true,
            'message' => "Command /{$command} executed",
            'note' => 'Check your Telegram for the response'
        ]);
    })->name('telegram.test-command');
}

// Only include tenant.php for tenant domains
if (!$isCentralDomain) {
    require __DIR__ . '/tenant.php';
}

// Routes for both central domains and tenant domains
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'my'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('language.switch');

// Tailwind test route
Route::get('/tailwind-test/{order}', function (Request $request,$order) {
    // dd(Order::pluck('id')->toArray());
    $order = App\Models\Order::find($order);
    return view('pdf.receipt', compact('order'));
    return view('tailwind-test');
})->name('tailwind.test');

// receipt preview
Route::get('/receipt-preview/{order}', function (App\Models\Order $order) {
    return view('pdf.receipt', compact('order'));
})->name('receipt.preview');


// Central domain routes - Will only work on padaung.test, etc.
if ($isCentralDomain) {
    Route::middleware(['web'])->group(function () {


        // Landing page for central domain
        Route::get('/', function () {
            $sellers = Seller::query()
                ->whereHas('media', function ($query) {
                    $query->where('collection_name', 'logos');
                })
                ->whereHas('addresses')
                ->get();
            return view('welcome', compact('sellers'));
        })->name('welcome');


        // Central domain seller registration
        Route::get('/partner/register', \App\Livewire\Seller\Register::class)->name('partner.register');

        // Product Import/Export routes
        Route::middleware(['auth'])->group(function () {
            Route::get('/products/export', [ProductImportExportController::class, 'exportProducts'])->name('products.export');
            Route::get('/variants/export', [ProductImportExportController::class, 'exportVariants'])->name('variants.export');
            Route::post('/products/import', [ProductImportExportController::class, 'importProducts'])->name('products.import');
            Route::post('/variants/import', [ProductImportExportController::class, 'importVariants'])->name('variants.import');
        });
    });
}
