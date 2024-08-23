<?php

use App\Filament\Seller\Pages\Pos;
use App\Http\Controllers\API\User\ProductController;
use App\Livewire\Invoice;
use Filament\Actions\Exports\Http\Controllers\DownloadExport;
use Filament\Actions\Imports\Http\Controllers\DownloadImportFailureCsv;
use Illuminate\Http\Request;
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

Route::get('invoice',Invoice::class)->name('invoice');
Route::get('pos',\App\Livewire\POS::class)->name('pos');

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');

//products
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/product/{productId}', [ProductController::class, 'getProductDetail']);

Route::get('/filament/exports/{export}/download', DownloadExport::class)
    ->name('filament.exports.download')->middleware('auth:seller');

Route::get('/filament/imports/{import}/failed-rows/download', DownloadImportFailureCsv::class)
    ->name('filament.imports.failed-rows.download')->middleware('auth:seller');


Route::get('print/{code}/{page}/{price}/{name}',function (Request $request){
    $code = $request->code;
    $page = $request->page;
    $price = $request->price;
    $name = $request->name;
    return view('livewire.print',compact('page','price','name','code'));
})->name('code.print');
