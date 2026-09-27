<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\TransaksiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


// =====================================================
// AUTH
// =====================================================

// Login - tidak membutuhkan token
Route::post('/login', [AuthController::class, 'login']);


// =====================================================
// ROUTE YANG MEMBUTUHKAN LOGIN
// =====================================================

Route::middleware('auth:sanctum')->group(function () {

    // =================================================
    // LOGOUT
    // =================================================

    Route::post('/logout', [AuthController::class, 'logout']);


    // =================================================
    // KATEGORI
    // =================================================

    // Admin dan Petugas dapat melihat kategori
    Route::get('/kategori', [KategoriController::class, 'index']);

    Route::get('/kategori/{kategori}', [KategoriController::class, 'show']);


    // Hanya Admin yang dapat menambah kategori
    Route::post('/kategori', [KategoriController::class, 'store'])
        ->middleware('role:admin');


    // Hanya Admin yang dapat mengubah kategori
    Route::put('/kategori/{kategori}', [KategoriController::class, 'update'])
        ->middleware('role:admin');


    // Hanya Admin yang dapat menghapus kategori
    Route::delete('/kategori/{kategori}', [KategoriController::class, 'destroy'])
        ->middleware('role:admin');


    // =================================================
    // BARANG
    // =================================================

    // Admin dan Petugas dapat melihat barang
    Route::get('/barang', [BarangController::class, 'index']);

    Route::get('/barang/{barang}', [BarangController::class, 'show']);


    // Hanya Admin yang dapat menambah barang
    Route::post('/barang', [BarangController::class, 'store'])
        ->middleware('role:admin');


    // Hanya Admin yang dapat mengubah barang
    Route::put('/barang/{barang}', [BarangController::class, 'update'])
        ->middleware('role:admin');


    // Hanya Admin yang dapat menghapus barang
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])
        ->middleware('role:admin');


    // =================================================
    // TRANSAKSI
    // =================================================

    // Admin dan Petugas dapat melihat riwayat transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index']);

    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show']);


    // Admin dan Petugas dapat melakukan transaksi
    // masuk maupun keluar
    Route::post('/transaksi', [TransaksiController::class, 'store'])
        ->middleware('role:admin,petugas');

});