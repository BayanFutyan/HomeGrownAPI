<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FcmTokenController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\ExhibitionRegistrationController;

    Route::apiResource('exhibitions', ExhibitionController::class);
    Route::get('/exhibitions/owner/{ownerId}', [ExhibitionController::class, 'getByOwner']);
    Route::post('/exhibitions/{exhibitionId}/invite-artisan', [ExhibitionRegistrationController::class, 'inviteArtisan']);
    Route::get(
    '/exhibitions/{exhibitionId}/registrations',
    [ExhibitionRegistrationController::class, 'getExhibitionRegistrations']
    );
    Route::put(
    '/exhibition-registrations/{registrationId}/status',
    [ExhibitionRegistrationController::class, 'updateStatus']
    );
    Route::get('/owners/{ownerId}/registrations', [ExhibitionRegistrationController::class, 'getOwnerRegistrations']);
    
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




    // Orders Routes
    Route::prefix('orders')->group(function () {
        Route::get('/seller/summary', [OrderController::class, 'ordersSummary']);
        Route::get('/seller/list/all', [OrderController::class, 'getAllSellerOrders']);  // ✅ تأكد من وجود هذا السطر
        Route::get('/seller/list', [OrderController::class, 'sellerOrders']);
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    });

    //Route::middleware('auth:sanctum')->post('/fcm-token', [FcmTokenController::class, 'store']);
    Route::post('/fcm-token', [FcmTokenController::class, 'store']);

    //http://127.0.0.1:8000/api/notifications/test

    Route::get('/notifications/test', [NotificationController::class, 'storeTest']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);