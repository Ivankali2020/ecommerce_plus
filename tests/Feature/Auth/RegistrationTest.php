<?php

namespace Tests\Feature\Auth;

use App\Livewire\Seller\Register as SellerRegister;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Livewire\Volt\Volt;
use RyanChandler\LaravelCloudflareTurnstile\Facades\Turnstile;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('new users can register', function () {
    $component = Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password');

    $component->call('register');

    $component->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('registration rejects a failed turnstile verification', function () {
    Turnstile::fake()->fail();

    View::share([
        'tenantSellerId' => null,
        'tenantSellerLogo' => '',
        'tenantEnableGoogleAuth' => false,
        'tenantSellerName' => 'Test Shop',
        'tenantSeller' => null,
    ]);

    Volt::test('pages.auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('cfTurnstileResponse', Turnstile::dummy())
        ->call('register')
        ->assertHasErrors(['cfTurnstileResponse']);

    $this->assertGuest();
});

test('seller registration rejects a failed turnstile verification', function () {
    Turnstile::fake()->fail();

    Livewire::test(SellerRegister::class)
        ->set('name', 'Test Seller')
        ->set('email', 'seller@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('domain', 'testshop')
        ->set('cfTurnstileResponse', Turnstile::dummy())
        ->call('register')
        ->assertHasErrors(['cfTurnstileResponse']);
});
