<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|==========================================
|> Auth Routes
|==========================================
*/

Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

/*
|==========================================
|> User Routes
|==========================================
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/user', [UserController::class, 'show'])->name('user.show');
    Route::post('/user', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|==========================================
|> Vendor Routes
|==========================================
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('vendor')->name('vendor.')->group(function () {
        Route::get('/', [VendorController::class, 'publicIndex'])->name('public.index');
        Route::post('/', [VendorController::class, 'store'])->name('store');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::put('/{vendor}', [VendorController::class, 'update'])->name('update');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');

        Route::post('/{vendor}/approve', [VendorController::class, 'approve'])->name('approve');
        Route::post('/{vendor}/reject', [VendorController::class, 'reject'])->name('reject');

        Route::get('/trashed', [VendorController::class, 'trashedVendors'])->name('trashed');
        Route::post('/{id}/restore', [VendorController::class, 'restore'])->name('restore');
    });

    Route::get('/dashboard/vendor', [VendorController::class, 'index'])->name('vendor.index');
});

/*
|==========================================
|> Region Routes
|==========================================
*/
Route::prefix('region')->name('region.')->group(function () {
    Route::get('/', [RegionController::class, 'index'])->name('index');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [RegionController::class, 'store'])->name('store');
        Route::put('/{region}', [RegionController::class, 'update'])->name('update');
        Route::delete('/{region}', [RegionController::class, 'destroy'])->name('destroy');
    });
});
