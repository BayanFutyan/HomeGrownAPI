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

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// هون خلي بس الأشياء اللي المستخدم العادي بقدر يشوفها بدون login
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/{id}/details', [ProductController::class, 'getDetails']);
Route::get('/products/{id}/comments', [ProductController::class, 'getComments']);

Route::get('/offers', [OfferController::class, 'index']);
Route::get('/offers/{id}', [OfferController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::get('/followers', [UserController::class, 'followers']);
        Route::get('/following', [UserController::class, 'following']);
        Route::post('/logout', [UserController::class, 'logout']);
    });

    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::post('/comments/{id}/toggle-like', [CommentController::class, 'toggleLike']);

    Route::get('/followers', [FollowerController::class, 'index']);
    Route::post('/followers', [FollowerController::class, 'store']);
    Route::delete('/followers/{id}', [FollowerController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    Route::post('/fcm-token', [FcmTokenController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    /*
    |--------------------------------------------------------------------------
    | Exhibition Routes
    |--------------------------------------------------------------------------
    */
    Route::apiResource('exhibitions', ExhibitionController::class);

    Route::get(
        '/exhibitions/owner/{ownerId}',
        [ExhibitionController::class, 'getByOwner']
    );

    Route::post(
        '/exhibitions/{exhibitionId}/invite-artisan',
        [ExhibitionRegistrationController::class, 'inviteArtisan']
    );

    Route::get(
        '/exhibitions/{exhibitionId}/registrations',
        [ExhibitionRegistrationController::class, 'getExhibitionRegistrations']
    );

    Route::put(
        '/exhibition-registrations/{registrationId}/status',
        [ExhibitionRegistrationController::class, 'updateStatus']
    );

    Route::get(
        '/owners/{ownerId}/registrations',
        [ExhibitionRegistrationController::class, 'getOwnerRegistrations']
    );
});

/*
|--------------------------------------------------------------------------
| Artisan Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:artisan'])->group(function () {

    // منتجات البائع الحالي فقط
    Route::get('/products', [ProductController::class, 'index']);

    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::put('/products/{id}/description', [ProductController::class, 'updateDescription']);
    Route::post('/products/{id}/offer', [ProductController::class, 'addOffer']);
    Route::delete('/products/{id}/offer', [ProductController::class, 'removeOffer']);
    Route::post('/products/{id}/details', [ProductController::class, 'addDetail']);
    Route::put('/products/{id}/all-details', [ProductController::class, 'updateAllDetails']);

    Route::delete('/product-details/{id}', [ProductDetailController::class, 'destroy']);

    Route::get('/orders/seller/summary', [OrderController::class, 'ordersSummary']);
    Route::get('/orders/seller/list/all', [OrderController::class, 'getAllSellerOrders']);
    Route::get('/orders/seller/list', [OrderController::class, 'sellerOrders']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
*/
Route::get('/notifications/test', [NotificationController::class, 'storeTest']);