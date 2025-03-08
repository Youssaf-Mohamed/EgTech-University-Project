<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Page\FollowingStoresController;
use App\Http\Controllers\Page\HomeController;

/*
|===================================================================
|> Home Route
|===================================================================
*/

Route::get('/following', [FollowingStoresController::class, 'index'])->name('following.index')->middleware('auth:sanctum');

Route::get('/home', [HomeController::class, 'index'])->name('home.index');
Route::get('/home/following', [HomeController::class, 'getFollowedProducts'])->name('home.following')->middleware('auth:sanctum');
