<?php

namespace App\Providers\Filament;

use App\Filament\Seller\Pages\EditProfile;
use App\Filament\Seller\Resources\OrderResource\Widgets\OrderChart;
use App\Helper\CheckFilamentSellerUser;
use App\Http\Middleware\AssignGlobeScope;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\RedirectIfUnauthenticated;
use App\Models\Seller;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('seller')
            ->path('seller')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
                'gray' => Color::Slate,
                'orange' => Color::Orange
            ])
            ->profile(EditProfile::class)
//            ->profile()
            ->authGuard('seller')
            ->discoverResources(in: app_path('Filament/Seller/Resources'), for: 'App\\Filament\\Seller\\Resources')
            ->discoverPages(in: app_path('Filament/Seller/Pages'), for: 'App\\Filament\\Seller\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Seller/Widgets'), for: 'App\\Filament\\Seller\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                OrderChart::class
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

            ])
//            ->spa(fn()=>  Str::contains(request()->userAgent(),'Chrome'))
            ->sidebarWidth('17rem')
            ->brandLogoHeight(fn() => Auth::guard('seller')->check() ? '3rem' : '5rem')
            ->brandLogo(fn() => Auth::guard('seller')->check() ? Seller::where('id',CheckFilamentSellerUser::getSellerId())->first()->getFirstMediaUrl('logos') : null) //->getFirstMediaUrl('logos')
            ->brandName(fn() => Auth::guard('seller')->check() ?   Seller::find(CheckFilamentSellerUser::getSellerId())->name : 'SELLER DASHBOARD')
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling(null) // this is testing purpose
                ->plugins([
                    FilamentShieldPlugin::make()
            ])
            ->navigationItems([
                NavigationItem::make('dashboard')
                    ->label(fn(): string => 'Go to Website')
                    ->url(fn(): string => route('store', CheckFilamentSellerUser::getSellerId()))
                    ->sort(sort: 10)
                    ->icon('heroicon-o-link'),
                NavigationItem::make('Create POS')
                    ->label(fn(): string => 'Create POS')
                    ->url(fn(): string => route('filament.seller.resources.orders.create'))
                    ->sort(sort: -1)
                    ->icon('heroicon-o-computer-desktop')
            ])
            // ->font('mm',url: asset('css/admin-font.css'), provider: LocalFontProvider::class)
            ->font('Jost')
            ->authMiddleware([
                RedirectIfUnauthenticated::class,
                // Authenticate::class,
            ]);
    }
}
