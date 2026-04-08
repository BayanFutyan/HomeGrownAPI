<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\UserController;

// Products Routes 
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    
    Route::put('/{id}/description', [ProductController::class, 'updateDescription']);
    Route::post('/{id}/offer', [ProductController::class, 'addOffer']);
    Route::delete('/{id}/offer', [ProductController::class, 'removeOffer']);
    Route::get('/{id}/details', [ProductController::class, 'getDetails']);
    Route::post('/{id}/details', [ProductController::class, 'addDetail']);
    Route::put('/{id}/all-details', [ProductController::class, 'updateAllDetails']);
    Route::get('/{id}/comments', [ProductController::class, 'getComments']);
});

// Comments Routes
Route::post('/comments', [CommentController::class, 'store']);
Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

// Product Details Routes
Route::delete('/product-details/{id}', [ProductDetailController::class, 'destroy']);
// Offers Routes
Route::get('/offers', [OfferController::class, 'index']);
Route::get('/offers/{id}', [OfferController::class, 'show']);

// Followers Routes
Route::get('/followers', [FollowerController::class, 'index']);
Route::post('/followers', [FollowerController::class, 'store']);
Route::delete('/followers/{id}', [FollowerController::class, 'destroy']);

// User Routes
Route::prefix('user')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/followers', [UserController::class, 'followers']);
    Route::get('/following', [UserController::class, 'following']);
});

Route::post('/comments/{id}/toggle-like', [CommentController::class, 'toggleLike']);