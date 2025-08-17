<?php

namespace App\Providers\Filament;

use App\Filament\Seller\Pages\EditProfile;
use App\Filament\Seller\Resources\OrderResource\Widgets\OrderChart;
use App\Filament\Seller\Widgets\Filters;
use App\Http\Middleware\AssignGlobeScope;
use App\Http\Middleware\EnsureSellerBelongsToTenant;
use App\Http\Middleware\RedirectIfSubscriptionExpired;
use App\Http\Middleware\SetLocale;
use App\Models\Tenant;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class SellerPanelProvider extends PanelProvider
{
    protected function getSeller()
    {
        $seller = Tenant::where('id', tenant('id'))->with('sellers')->first()->sellers->first();
        if (! $seller) {
            // Handle case where no seller is found, e.g., throw an exception or return a default value
            Auth::logout();
            abort(404, 'Seller not found for the current tenant.');
        }

        return Tenant::where('id', tenant('id'))->with('sellers')->first()->sellers->first();
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('seller')
            ->path('seller')
            ->authGuard('seller')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
                'orange' => Color::Orange,
                'green' => Color::Green,
            ])
            ->profile(EditProfile::class)
            // ->registration()
            ->passwordReset()
            ->emailVerification()
            ->discoverResources(in: app_path('Filament/Seller/Resources'), for: 'App\\Filament\\Seller\\Resources')
            ->discoverPages(in: app_path('Filament/Seller/Pages'), for: 'App\\Filament\\Seller\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Seller/Widgets'), for: 'App\\Filament\\Seller\\Widgets')
            ->widgets([
                //                Widgets\AccountWidget::class,
                //                Widgets\FilamentInfoWidget::class,
                Filters::class,
                OrderChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                AssignGlobeScope::class,
                InitializeTenancyByDomain::class,
                PreventAccessFromCentralDomains::class,
                EnsureSellerBelongsToTenant::class,
                RedirectIfSubscriptionExpired::class,
                SetLocale::class,
            ], true)
            //            ->spa(fn()=>  Str::contains(request()->userAgent(),'Chrome'))
            ->sidebarWidth('17rem')
            ->brandLogoHeight(fn () => Auth::guard('seller')->check() ? '3rem' : '5rem')
            ->brandLogo(fn () => self::getSeller()->getFirstMediaUrl('logos')) // ->getFirstMediaUrl('logos')
            ->brandName(fn () => self::getSeller()->name ?? 'EMPTY')
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling(null) // this is testing purpose
            ->maxContentWidth('full')
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->navigationItems([
                NavigationItem::make('dashboard')
                    ->label(fn (): string => 'Go to Website')
                    ->url(fn (): string => route('welcome'))
                    ->sort(sort: 10)
                    ->icon('heroicon-o-link'),
                NavigationItem::make('Simple POS')
                    ->label(fn (): string => 'Simple POS')
                    ->url(fn (): string => route('seller.pos'))
                    ->sort(sort: -1)
                    ->icon('heroicon-o-computer-desktop'),
                NavigationItem::make('Advance POS PRO')
                    ->label(fn (): string => 'Advance POS PRO')
                    ->url(fn (): string => route('filament.seller.resources.orders.create'))
                    ->sort(sort: -1)
                    ->icon('heroicon-o-computer-desktop'),
            ])
            // ->font('mm',url: asset('css/admin-font.css'), provider: LocalFontProvider::class)
            // ->font('Jost')
            ->databaseTransactions()
            ->authMiddleware([
                // RedirectIfUnauthenticated::class,
                Authenticate::class,

            ]);
    }
}
