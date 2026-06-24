<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Alyani\Subsystem\Http\Middleware\DetectLanguage;
use Alyani\Subsystem\Http\Controllers\Api\{
    AuthController,
    ProfileController,
    StorageController,
    FaqController,
    ArticleController,
    PaymentController,
};
use Alyani\Subsystem\Http\Middleware\Download;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Force HTTPS in production
if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

/**
 * Guest Routes
 */
Route::middleware('throttle:5,15')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/user/sendOTP', 'sendOTP')->name('user.sendOTP');
        Route::post('/user/register', 'register')->name('user.register');
        Route::post('/user/login', 'login')->name('user.login');
    });
});

/**
 * Authenticated Routes (with authorization)
 */
Route::middleware(['auth:sanctum'])->group(function () {
    // User
    Route::post('/user/logout', [AuthController::class, 'logout'])->name('user.logout');

    // Profile Routes
    Route::controller(ProfileController::class)->group(function () {
        Route::post('/profile/set', 'set')->name('profile.set');
        Route::post('/profile/setMobile', 'setMobile')->name('profile.setMobile');
        Route::post('/profile/verifyMobile', 'verifyMobile')->name('profile.verifyMobile');
        Route::post('/profile/setPassword', 'setPassword')->name('profile.setPassword');
        Route::post('/profile/changePassword', 'changePassword')->name('profile.changePassword');
    });

    // Payment Routes
    Route::controller(PaymentController::class)->group(function () {
        Route::post('payments', 'list')->name('payment.list');
    });

    // Storage Routes
    Route::controller(StorageController::class)->group(function () {
        Route::post('storage/upload', 'upload')->name('storage.upload');
    });
});

// Storage get signed link to doanload
Route::post('/storage/get-link/{type}/{SID}', [StorageController::class, 'generateLink'])
    ->withoutMiddleware(['encryption'])
    ->middleware([
        Download::class,
    ])
    ->name('storage.generateLink');


Route::middleware(DetectLanguage::class)->group(function () {
    // FAQ Routes
    Route::prefix('faq')->controller(FaqController::class)->group(function () {
        Route::post('/list', 'list')->name('faq.list');
        Route::post('category/list', 'listByCategory')->name('faq.category.list');
    });

    // Article Routes
    Route::prefix('/article')->controller(ArticleController::class)->group(function () {
        Route::post('/category', 'category')->name('article.category');
        Route::post('/list', 'list')->name('article.list');
        Route::post('/get', 'get')->name('article.get');
        // Route::post('/related', 'related')->name('article.related');
    });
});
