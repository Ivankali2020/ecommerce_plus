<?php

use App\Http\Controllers\ProductImportExportController;
use App\Models\Seller;
use Illuminate\Support\Facades\Route;


// Check if the current domain is a central domain or a tenant domain
$isCentralDomain = in_array(request()->getHost(), config('tenancy.central_domains'));

// Only include auth.php for central domains
if ($isCentralDomain) {
    require __DIR__ . '/auth.php';
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
