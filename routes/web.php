<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

// =====================
// TEST RAJAONGKIR (FIXED)
// =====================
Route::get('/list-ongkir', function () {
    $response = Http::withHeaders([
        'key' => 'ncSSgM4Q4549ff2f1e916eabbMYBKI4N'
    ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

    dd($response->json());
});

Route::get('/', function () {
    return redirect()->route('beranda');
});

// =====================
// HALAMAN CEK ONGKIR
// =====================
Route::get('/cek-ongkir', function () {
    return view('ongkir');
});

// =====================
// BACKEND
// =====================
Route::get('backend/beranda', [BerandaController::class, 'berandaBackend'])
    ->name('backend.beranda')->middleware('auth');

Route::get('backend/login', [LoginController::class, 'loginBackend'])
    ->name('backend.login');
Route::post('backend/login', [LoginController::class, 'authenticateBackend'])
    ->name('backend.login');
Route::post('backend/logout', [LoginController::class, 'logoutBackend'])
    ->name('backend.logout');

// User
Route::resource('backend/user', UserController::class, ['as' => 'backend'])
    ->middleware('auth');
Route::get('backend/laporan/formuser', [UserController::class, 'formUser'])
    ->name('backend.laporan.formuser')->middleware('auth');
Route::post('backend/laporan/cetakuser', [UserController::class, 'cetakUser'])
    ->name('backend.laporan.cetakuser')->middleware('auth');

// Kategori
Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend'])
    ->middleware('auth');

// Produk
Route::resource('backend/produk', ProdukController::class, ['as' => 'backend'])
    ->middleware('auth');
Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])
    ->name('backend.foto_produk.store')->middleware('auth');
Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])
    ->name('backend.foto_produk.destroy')->middleware('auth');
Route::get('backend/laporan/formproduk', [ProdukController::class, 'formProduk'])
    ->name('backend.laporan.formproduk')->middleware('auth');
Route::post('backend/laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])
    ->name('backend.laporan.cetakproduk')->middleware('auth');

// Customer
Route::resource('backend/customer', CustomerController::class, ['as' => 'backend'])
    ->middleware('auth');
Route::get('backend/laporan/formcustomer', [CustomerController::class, 'formCustomer'])
    ->name('backend.laporan.formcustomer')->middleware('auth');
Route::post('backend/laporan/cetakcustomer', [CustomerController::class, 'cetakCustomer'])
    ->name('backend.laporan.cetakcustomer')->middleware('auth');

// =====================
// FRONTEND
// =====================
Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');
Route::get('/produk/detail/{id}', [ProdukController::class, 'detail'])->name('produk.detail');
Route::get('/produk/kategori/{id}', [ProdukController::class, 'produkKategori'])->name('produk.kategori');
Route::get('/produk/all', [ProdukController::class, 'produkAll'])->name('produk.all');

// Google Auth
Route::get('/auth/redirect', [CustomerController::class, 'redirect'])->name('auth.redirect');
Route::get('/auth/google/callback', [CustomerController::class, 'callback'])->name('auth.callback');

// Logout
Route::post('/logout', [CustomerController::class, 'logout'])->name('logout');

// Customer & Order
Route::get('/customer/akun/{id}', [CustomerController::class, 'akun'])->name('customer.akun');
Route::put('/customer/updateakun/{id}', [CustomerController::class, 'updateAkun'])->name('customer.updateakun');

// CART & ONGKIR (INI YANG DIPAKAI)
Route::post('add-to-cart/{id}', [OrderController::class, 'addToCart'])->name('order.addToCart');
Route::get('cart', [OrderController::class, 'viewCart'])->name('order.cart');
Route::post('cart/update/{id}', [OrderController::class, 'updateCart'])->name('order.updateCart');
Route::post('remove/{id}', [OrderController::class, 'removeFromCart'])->name('order.remove');

// ONGKIR API (PAKAI INI)
Route::get('provinces', [OrderController::class, 'getProvinces']);
Route::get('cities', [OrderController::class, 'getCities']);
Route::post('cost', [OrderController::class, 'getCost']);

Route::post('updateongkir', [OrderController::class, 'updateongkir'])->name('order.updateongkir');

// PAYMENT
Route::get('select-payment', [OrderController::class, 'selectPayment'])->name('order.selectpayment');
Route::get('order/complete', [OrderController::class, 'complete'])->name('order.complete');
Route::get('order/history', [OrderController::class, 'orderHistory'])->name('order.history');
Route::get('order/invoice/{id}', [OrderController::class, 'invoiceFrontend'])->name('order.invoiceFrontend');

// Midtrans callback
Route::post('midtrans/callback', [OrderController::class, 'callback'])->name('midtrans.callback');