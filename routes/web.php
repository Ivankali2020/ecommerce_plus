<?php

use App\Livewire\Invoice;
use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');

Route::get('invoice',Invoice::class)->name('invoice');

Route::get('/filament/exports/{export}/download', DownloadExport::class)
    ->name('filament.exports.download')->middleware('auth:seller');

Route::get('/filament/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('filament.imports.failed-rows.download')->middleware('auth:seller');
