<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Page\HomeController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProductDetailController;
use App\Http\Controllers\Api\AdvertisementController;
use App\Http\Controllers\Api\NotificationController;
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
    Route::get('/user', [UserController::class, 'currentUser'])->name('user.current');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
    Route::post('/users/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
Route::get('/verify/{token}', [UserController::class, 'verifyAccount'])->name('verify.account');

/*
|===================================================================
|> Vendor Management Routes (Protected)
|===================================================================
*/
Route::prefix('vendor')->as('vendor.')->group(function () {
    Route::get('/', [VendorController::class, 'publicIndex'])->name('public.index');
        Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/trashed', [VendorController::class, 'trashedVendors'])->name('trashed-vendors');
        Route::post('/', [VendorController::class, 'store'])->name('store');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::post('/{vendor}', [VendorController::class, 'update'])->name('update');
        Route::delete('/{vendor}', [VendorController::class, 'destroy'])->name('destroy');

        Route::post('/{vendor}/approve', [VendorController::class, 'approve'])->name('approve');
        Route::post('/{vendor}/reject', [VendorController::class, 'reject'])->name('reject');

        Route::post('/{id}/restore', [VendorController::class, 'restore'])->name('restore');
    });
});
Route::get('/dashboard/vendor', [VendorController::class, 'index'])->name('vendor.index')->middleware('auth:sanctum');

/*
|===================================================================
|> Region Management Routes (Protected)
|===================================================================
*/
Route::prefix('region')->as('region.')->group(function () {
    Route::get('/', [RegionController::class, 'index'])->name('index');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [RegionController::class, 'store'])->name('store');
        Route::post('/{region}', [RegionController::class, 'update'])->name('update');
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
        Route::post('/{vendor}', [FollowController::class, 'toggleFollow'])->name('toggle');
        Route::get('/vendors', [FollowController::class, 'getFollowedVendors'])->name('vendors');
        Route::get('/activities', [FollowController::class, 'getLatestActivities'])->name('activities');
    });
});

/*
|===================================================================
|> Promotion Management Routes (Protected)
|===================================================================
*/
Route::prefix('promotion')->as('promotion.')->group(function () {
    Route::get('/', [PromotionController::class, 'index'])->name('index');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/active', [PromotionController::class, 'vendorSubscriptions'])->name('active');
        Route::post('/', [PromotionController::class, 'store'])->name('store');
        Route::post('/{promotion}', [PromotionController::class, 'update'])->name('update');
        Route::delete('/{promotion}', [PromotionController::class, 'destroy'])->name('destroy');
        Route::post('/vendors/{vendor}/promotions/{promotion}/subscribe', [PromotionController::class, 'subscribe'])->name('vendors.promotions.subscribe');
        Route::put('/vendors/{vendor}/promotions/{promotion}/approve', [PromotionController::class, 'approveSubscription'])->name('vendors.promotions.approve');
        Route::put('/vendors/{vendor}/promotions/{promotion}/reject', [PromotionController::class, 'rejectSubscription'])->name('vendors.promotions.reject');
    });
});
/*
|===================================================================
|> Category Management Routes (Protected)
|===================================================================
*/
Route::prefix('category')->name('category.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::post('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });
});

/*
|===================================================================
|> Category Management Routes (Protected)
|===================================================================
*/
Route::prefix('product')->name('product.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::post('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });
});

/*
|===================================================================
|> Product Details Management Routes (Protected)
|===================================================================
*/
Route::prefix('product/{product}/details')->name('product.details.')->group(function () {
    Route::get('/', [ProductDetailController::class, 'index'])->name('index');
    Route::get('/{detail}', [ProductDetailController::class, 'show'])->name('show');
        Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ProductDetailController::class, 'store'])->name('store');
        Route::post('/{detail}', [ProductDetailController::class, 'update'])->name('update');
        Route::delete('/{detail}', [ProductDetailController::class, 'destroy'])->name('destroy');
    });
});

/*
|===================================================================
|> Favorite Management Routes (Protected)
|===================================================================
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('favorite')->name('favorite.')->group(function () {
        Route::post('/toggle/{product}', [FavoriteController::class, 'toggle'])->name('toggle');
        Route::delete('/remove/{product}', [FavoriteController::class, 'remove'])->name('remove');
        Route::get('/', [FavoriteController::class, 'index'])->name('index');
        Route::get('/check/{product}', [FavoriteController::class, 'check'])->name('check');
    });
});

/*
|===================================================================
|> Reviews Management Routes (Protected)
|===================================================================
*/
Route::prefix('product/{product}/reviews')->name('product.reviews.')->group(function () {
    Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ReviewController::class, 'store'])->name('store');
        Route::post('/{review}', [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
    });
});

/*
|===================================================================
|> Advertisement Management Routes (Protected)
|===================================================================
*/
Route::prefix('advertisement')->as('advertisement.')->group(function () {
    Route::get('/', [AdvertisementController::class, 'index'])->name('index');
    Route::get('/{advertisement}', [AdvertisementController::class, 'show'])->name('show');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [AdvertisementController::class, 'store'])->name('store');
        Route::post('/{advertisement}', [AdvertisementController::class, 'update'])->name('update');
        Route::delete('/{advertisement}', [AdvertisementController::class, 'destroy'])->name('destroy');
    });
});

/*
|===================================================================
|> Notification Management Routes (Protected)
|===================================================================
*/
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    });
});

require __DIR__ . '/server.php';
require __DIR__ . '/app.php';
