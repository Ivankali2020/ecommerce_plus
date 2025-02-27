<?php

use App\Livewire\ProductDetail;
use App\Livewire\Store;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';


Route::get('store/{id}', Store::class)->name('store');
Route::get('product/{product}', ProductDetail::class)->name('product');