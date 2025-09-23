<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Seller;
use App\Models\Variant;
use App\Observers\CartObserver;
use App\Observers\SellerObserver;
use App\Observers\WholeSaleObserver;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->bind(Authenticatable::class, Seller::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

        // Set Livewire update route with conditional tenant middleware
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware([
                    'web',
                    \App\Http\Middleware\ConditionalTenancyMiddleware::class,
                ]);
        });

        // Update the middleware used by the asset controller
        TenantAssetsController::$tenancyMiddleware = InitializeTenancyByDomainOrSubdomain::class;

        // Register observers
        Cart::observe(CartObserver::class);
        Seller::observe(SellerObserver::class);
        Product::observe(WholeSaleObserver::class);
        Variant::observe(WholeSaleObserver::class);

        Model::unguard();

        Model::automaticallyEagerLoadRelationships();

        // Configure Filament for v4 compatibility
        // \Filament\Forms\Components\FileUpload::configureUsing(function (\Filament\Forms\Components\FileUpload $fileUpload) {
        //     $fileUpload->visibility('public');
        // });

        // \Filament\Tables\Columns\ImageColumn::configureUsing(function (\Filament\Tables\Columns\ImageColumn $imageColumn) {
        //     $imageColumn->visibility('public');
        // });

        // \Filament\Infolists\Components\ImageEntry::configureUsing(function (\Filament\Infolists\Components\ImageEntry $imageEntry) {
        //     $imageEntry->visibility('public');
        // });

        InitializeTenancyByDomain::$onFail = function ($exception, $request, $next) {
            return redirect(env('APP_URL'));
        };

        // Note: Tenant data sharing moved to ShareTenantData middleware for better performance

        // Bind custom registration response for seller panel
        // $this->app->bind(
        //     \Filament\Http\Responses\Auth\Contracts\RegistrationResponse::class,
        //     \App\Http\Responses\Auth\RegistrationResponse::class,
        // );

        // Register Filament CSS using Vite
        FilamentAsset::register([
            Css::make('filament-theme', Vite::asset('resources/css/filament.css')),
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): View => view('components.language-switcher'),
        );

        // Register custom footer with contact information
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::PAGE_FOOTER_WIDGETS_AFTER,
        //     fn (): View => view('components.contact-footer'),
        // );
        // Register the subscription info card in the seller panel sidebar
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_AFTER,
            fn (): string => Blade::render('@if(request()->routeIs(\'filament.seller.*\')) @livewire(\'seller.subscription-info-card\') @endif'),
        );

        // Register custom Filament scripts only for order-products page
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::SCRIPTS_AFTER,
            function () {
                // Only load script on order-products page
                if (request()->is('*/order-products*')) {
                    return '<script src="'.asset('js/filament-table-sorting.js').'"></script>';
                }

                return '';
            }
        );
    }
}
