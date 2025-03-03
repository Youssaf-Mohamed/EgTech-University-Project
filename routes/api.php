<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PromotionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|===================================================================
|> Authentication Routes
|===================================================================
*/
// Auth Routes
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

/*
|===================================================================
|> User Management Routes (Protected)
|===================================================================
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/user', [UserController::class, 'show'])->name('user.show');
    Route::post('/user', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|===================================================================
|> Vendor Management Routes (Protected)
|===================================================================
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('vendor')->as('vendor.')->group(function () {
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
|===================================================================
|> Region Management Routes (Protected)
|===================================================================
*/
Route::prefix('region')->as('region.')->group(function () {
    Route::get('/', [RegionController::class, 'index'])->name('index');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [RegionController::class, 'store'])->name('store');
        Route::put('/{region}', [RegionController::class, 'update'])->name('update');
        Route::delete('/{region}', [RegionController::class, 'destroy'])->name('destroy');
    });
});

/*
|===================================================================
|> Follow Management Routes (Protected)
|===================================================================
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('follow')->as('follow.')->group(function () {
        Route::post('/follow/{vendor}', [FollowController::class, 'toggleFollow'])->name('toggle');
        Route::get('/vendors', [FollowController::class, 'getFollowedVendors'])->name('vendors');
        Route::get('/activities', [FollowController::class, 'getLatestActivities'])->name('activities');
    });
});

/*
|===================================================================
|> Promotion Management Routes (Protected)
|===================================================================
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('promotion')->as('promotion.')->group(function () {
        Route::get('/', [PromotionController::class, 'index'])->name('index');
        Route::post('/', [PromotionController::class, 'create'])->name('create');
        Route::put('/{promotion}', [PromotionController::class, 'update'])->name('update');
        Route::delete('/{promotion}', [PromotionController::class, 'destroy'])->name('destroy');

        Route::post('/vendors/{vendor}/promotion/{promotion}/subscribe', [PromotionController::class, 'subscribe'])->name('vendors.promotion.subscribe');
        Route::put('/vendors/{vendor}/promotion/{promotion}/approve', [PromotionController::class, 'approveSubscription'])->name('vendors.promotion.approve');
        Route::put('/vendors/{vendor}/promotion/{promotion}/reject', [PromotionController::class, 'rejectSubscription'])->name('vendors.promotion.reject');
    });
});
