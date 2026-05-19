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
use App\Http\Controllers\ActivityController;
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

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('user')->group(function () {

        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);

        Route::get('/followers', [UserController::class, 'followers']);
        Route::get('/following', [UserController::class, 'following']);

        Route::post('/logout', [UserController::class, 'logout']);
    });

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
    Route::post('/comments/{id}/toggle-like', [CommentController::class, 'toggleLike']);

    /*
    |--------------------------------------------------------------------------
    | Followers
    |--------------------------------------------------------------------------
    */

    Route::get('/followers', [FollowerController::class, 'index']);
    Route::post('/followers', [FollowerController::class, 'store']);
    Route::delete('/followers/{id}', [FollowerController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::post('/fcm-token', [FcmTokenController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    /*
    |--------------------------------------------------------------------------
    | Customer Routes
    |--------------------------------------------------------------------------
    */

    Route::prefix('customer')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Stories
        |--------------------------------------------------------------------------
        */

        Route::get('/stories', [\App\Http\Controllers\Customer\StoryController::class, 'index']);
        Route::get('/stories/{id}', [\App\Http\Controllers\Customer\StoryController::class, 'show']);
        Route::post('/stories/{id}/view', [\App\Http\Controllers\Customer\StoryController::class, 'view']);

        Route::get('/my-stories', [\App\Http\Controllers\Customer\StoryController::class, 'myStories']);
        Route::get('/stories/{id}/views', [\App\Http\Controllers\Customer\StoryController::class, 'views']);

        Route::post('/stories', [\App\Http\Controllers\Customer\StoryController::class, 'store']);
        Route::delete('/stories/{id}', [\App\Http\Controllers\Customer\StoryController::class, 'destroy']);

        /*
        |--------------------------------------------------------------------------
        | Artisan Search
        |--------------------------------------------------------------------------
        */

        Route::get('/artisans/search', [UserController::class, 'searchArtisans']);

    // Artisan Profile
    Route::get('/artisans/{id}', [UserController::class, 'getArtisanProfile']);
    Route::get('/artisans/{id}/products', [UserController::class, 'getArtisanProducts']);
    Route::get('/artisans/{id}/posts', [UserController::class, 'getArtisanPosts']);

    // Follow
    Route::post('/artisans/{id}/follow', [UserController::class, 'followArtisan']);
    Route::delete('/artisans/{id}/follow', [UserController::class, 'unfollowArtisan']);
    Route::get('/artisans/{id}/check-follow', [UserController::class, 'checkFollowArtisan']);

    // Rating
    Route::post('/artisans/{id}/rate', [UserController::class, 'rateArtisan']);
    Route::get('/artisans/{id}/my-rating', [UserController::class, 'getMyRatingForArtisan']);
        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        Route::get('/products', [\App\Http\Controllers\Customer\ProductController::class, 'index']);
        Route::get('/products/{id}', [\App\Http\Controllers\Customer\ProductController::class, 'show']);
        Route::get('/products/{id}/details', [\App\Http\Controllers\Customer\ProductController::class, 'details']);

        /*
        |--------------------------------------------------------------------------
        | Product Likes
        |--------------------------------------------------------------------------
        */

        Route::post('/like', [\App\Http\Controllers\Customer\LikeController::class, 'store']);
        Route::delete('/like', [\App\Http\Controllers\Customer\LikeController::class, 'destroy']);

        Route::get('/my-likes', [\App\Http\Controllers\Customer\LikeController::class, 'myLikes']);

        Route::post('/check-like', [\App\Http\Controllers\Customer\LikeController::class, 'check']);

        /*
        |--------------------------------------------------------------------------
        | Posts
        |--------------------------------------------------------------------------
        */

        // عرض المنشورات
        Route::get('/posts', [\App\Http\Controllers\Customer\PostController::class, 'index']);
        Route::get('/posts/{id}', [\App\Http\Controllers\Customer\PostController::class, 'show']);

        // إنشاء وتعديل وحذف المنشورات
        Route::post('/posts', [\App\Http\Controllers\Customer\PostController::class, 'store']);
        Route::put('/posts/{id}', [\App\Http\Controllers\Customer\PostController::class, 'update']);
        Route::delete('/posts/{id}', [\App\Http\Controllers\Customer\PostController::class, 'destroy']);

        // منشورات مستخدم معين
        Route::get('/users/{userId}/posts', [\App\Http\Controllers\Customer\PostController::class, 'getUserPosts']);

        // لايكات المنشورات
        Route::post('/posts/{id}/like', [\App\Http\Controllers\Customer\PostController::class, 'like']);
        Route::delete('/posts/{id}/like', [\App\Http\Controllers\Customer\PostController::class, 'unlike']);

        // تعليقات المنشورات
        Route::post('/posts/{id}/comments', [\App\Http\Controllers\Customer\PostController::class, 'addComment']);

        Route::get('/posts/{id}/comments', [\App\Http\Controllers\Customer\PostController::class, 'getComments']);

        Route::delete('/posts/comments/{commentId}', [\App\Http\Controllers\Customer\PostController::class, 'deleteComment']);

        /*
        |--------------------------------------------------------------------------
        | Activities
        |--------------------------------------------------------------------------
        */

        Route::get('/activities', [\App\Http\Controllers\Customer\ActivityController::class, 'index']);

        Route::get('/activities/type/{type}', [\App\Http\Controllers\Customer\ActivityController::class, 'getByType']);

        Route::put('/activities/{id}/read', [\App\Http\Controllers\Customer\ActivityController::class, 'markAsRead']);

        Route::put('/activities/mark-all-read', [\App\Http\Controllers\Customer\ActivityController::class, 'markAllAsRead']);

        /*
        |--------------------------------------------------------------------------
        | Saves
        |--------------------------------------------------------------------------
        */

        Route::post('/save', [\App\Http\Controllers\Customer\SaveController::class, 'store']);

        Route::delete('/save/{saveId}', [\App\Http\Controllers\Customer\SaveController::class, 'destroy']);

        Route::get('/my-saves', [\App\Http\Controllers\Customer\SaveController::class, 'mySaves']);

        // ✅ Saved Posts & Products
        Route::get('/saved-posts', [\App\Http\Controllers\Customer\SaveController::class, 'savedPosts']);

        Route::get('/saved-products', [\App\Http\Controllers\Customer\SaveController::class, 'savedProducts']);

        // ✅ Check Save
        Route::post('/check-save', [\App\Http\Controllers\Customer\SaveController::class, 'check']);

        Route::post('/save-count', [\App\Http\Controllers\Customer\SaveController::class, 'count']);
    });

    /*
    |--------------------------------------------------------------------------
    | Exhibition Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('exhibitions', ExhibitionController::class);
    Route::get('/my-exhibitions', [ExhibitionController::class, 'myExhibitions']);
    // في api.php
    // Route::post('/exhibitions/{id}/update-with-image', [ExhibitionController::class, 'updateWithImage']);
// في api.php
Route::post('/exhibitions/{id}/upload-image', [ExhibitionController::class, 'uploadImage']);
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

    // Route::get(
    //     '/owners/{ownerId}/registrations',
    //     [ExhibitionRegistrationController::class, 'getOwnerRegistrations']
    // );

    Route::get('/my-registrations', 
    [ExhibitionRegistrationController::class, 'getOwnerRegistrations']);
});

/*
|--------------------------------------------------------------------------
| Artisan Routes
|--------------------------------------------------------------------------
*/

Route::prefix('artisan')->middleware(['auth:sanctum', 'role:artisan'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */

    Route::get('/products', [ProductController::class, 'index']);

    Route::post('/products', [ProductController::class, 'store']);

    Route::put('/products/{id}', [ProductController::class, 'update']);

    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Product Description & Details
    |--------------------------------------------------------------------------
    */

    Route::put('/products/{id}/description', [ProductController::class, 'updateDescription']);

    Route::post('/products/{id}/details', [ProductController::class, 'addDetail']);

    Route::put('/products/{id}/all-details', [ProductController::class, 'updateAllDetails']);

    Route::delete('/product-details/{id}', [ProductDetailController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Offers
    |--------------------------------------------------------------------------
    */

    Route::post('/products/{id}/offer', [ProductController::class, 'addOffer']);

    Route::delete('/products/{id}/offer', [ProductController::class, 'removeOffer']);

    /*
    |--------------------------------------------------------------------------
    | Seller Orders
    |--------------------------------------------------------------------------
    */

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