<?php

use Alyani\Subsystem\Http\Controllers\Web\AdminAuthController;
use Alyani\Subsystem\Http\Controllers\Web\ArticleCategoryController;
use Alyani\Subsystem\Http\Controllers\Web\ArticleController;
use Alyani\Subsystem\Http\Controllers\Web\DashboardController;
use Alyani\Subsystem\Http\Controllers\Web\FaqCategoryController;
use Alyani\Subsystem\Http\Controllers\Web\FaqController;
use Alyani\Subsystem\Http\Controllers\Web\HeavyUploaderController;
use Alyani\Subsystem\Http\Controllers\Web\IpgController;
use Alyani\Subsystem\Http\Controllers\Web\ManagerController;
use Alyani\Subsystem\Http\Controllers\Web\PaymentController;
use Alyani\Subsystem\Http\Controllers\Web\RoleController;
use Alyani\Subsystem\Http\Controllers\Web\StorageController;
use Alyani\Subsystem\Http\Controllers\Web\TinymceController;
use Alyani\Subsystem\Http\Controllers\Web\TransactionController;
use Alyani\Subsystem\Http\Controllers\Web\UserController;
use Alyani\Subsystem\Http\Controllers\Web\UserManageBalanceController;
use Alyani\Subsystem\Http\Controllers\Web\WithdrawalController;
use Alyani\Subsystem\Http\Middleware\Download;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

Route::group(['prefix' => 'admin'], function () {

    // Admin guest routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'login'])->name('login');
        Route::get('reloadCaptcha', [AdminAuthController::class, 'reloadCaptcha'])->name('reloadCaptcha');
        Route::post('handleLogin', [AdminAuthController::class, 'handleLogin'])->name('handleLogin');
    });

    // Admin authenticated routes without access check
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Tinymce uploader
        Route::post('/tinymce/upload', [TinymceController::class, 'upload'])->name('tinymce.upload');

        // Heavy uploader
        Route::prefix('heavy')->controller(HeavyUploaderController::class)
            ->group(function () {
                Route::post('/upload', 'upload')->name('heavy.upload');
                Route::post('/delete', 'delete')->name('heavy.delete');
            });
    });

    // Admin authenticated + access routes
    Route::middleware(['auth:sanctum', 'checkPermission'])->group(function () {
        // Manager
        Route::prefix('manager')
            ->controller(ManagerController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.manager.list');
                Route::get('/create', 'create')->name('admin.manager.create');
                Route::post('/store', 'store')->name('admin.manager.store');
                Route::get('/edit/{manager}', 'edit')->name('admin.manager.edit');
                Route::post('/update/{manager}', 'update')->name('admin.manager.update');
            });

        // User
        Route::prefix('user')
            ->controller(UserController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.user.list');
                Route::get('/show/{user}', 'show')->name('admin.user.show');
                Route::get('/create', 'create')->name('admin.user.create');
                Route::post('/store', 'store')->name('admin.user.store');
                Route::get('/edit/{user}', 'edit')->name('admin.user.edit');
                Route::post('/update/{user}', 'update')->name('admin.user.update');
                Route::get('/updateStatus/{user}', 'updateStatus')->name('admin.user.updateStatus');
                Route::get('/delete/{user}', 'delete')->name('admin.user.delete');
            });

        // User manage balance
        Route::prefix('user/manageBalance')
            ->controller(UserManageBalanceController::class)
            ->middleware('checkPermission:admin.userManageBalance')
            ->group(function () {
                Route::get('/{user}', 'manageBalance')->name('admin.userManageBalance');
                Route::post('/{user}/increase', 'increase')->name('admin.userManageBalance.increase');
                Route::post('/{user}/decrease', 'decrease')->name('admin.userManageBalance.decrease');
            });

        // Payment
        Route::prefix('payment')
            ->controller(PaymentController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.payment.list');
            });

        // Withdrawal
        Route::prefix('withdrawal')
            ->controller(WithdrawalController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.withdrawal.list');
            });

        // Transaction
        Route::prefix('transaction')
            ->controller(TransactionController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.transaction.list');
            });

        // Faq category
        Route::prefix('faqCategory')
            ->controller(FaqCategoryController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.faqCategory.list');
                Route::get('/create', 'create')->name('admin.faqCategory.create');
                Route::post('/store', 'store')->name('admin.faqCategory.store');
                Route::get('/edit/{faqCategory}', 'edit')->name('admin.faqCategory.edit');
                Route::post('/update/{faqCategory}', 'update')->name('admin.faqCategory.update');
                Route::get('/archive/{faqCategory}', 'archive')->name('admin.faqCategory.archive');
                Route::get('/unarchive/{faqCategory}', 'unarchive')->name('admin.faqCategory.unarchive');
            });

        // Faq
        Route::prefix('faq')
            ->controller(FaqController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.faq.list');
                Route::get('/create', 'create')->name('admin.faq.create');
                Route::post('/store', 'store')->name('admin.faq.store');
                Route::get('/edit/{faq}', 'edit')->name('admin.faq.edit');
                Route::post('/update/{faq}', 'update')->name('admin.faq.update');
                Route::get('/delete/{faq}', 'delete')->name('admin.faq.delete');
            });

        // Article category
        Route::prefix('articleCategory')
            ->controller(ArticleCategoryController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.articleCategory.list');
                Route::get('/create', 'create')->name('admin.articleCategory.create');
                Route::post('/store', 'store')->name('admin.articleCategory.store');
                Route::get('/edit/{articleCategory}', 'edit')->name('admin.articleCategory.edit');
                Route::post('/update/{articleCategory}', 'update')->name('admin.articleCategory.update');
                Route::get('/delete/{articleCategory}', 'delete')->name('admin.articleCategory.delete');
            });

        // Article
        Route::prefix('article')
            ->controller(ArticleController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.article.list');
                Route::get('/show/{article}', 'show')->name('admin.article.show');
                Route::get('/create', 'create')->name('admin.article.create');
                Route::post('/store', 'store')->name('admin.article.store');
                Route::get('/edit/{article}', 'edit')->name('admin.article.edit');
                Route::post('/update/{article}', 'update')->name('admin.article.update');
                Route::get('/delete/{article}', 'delete')->name('admin.article.delete');
            });

        // Role
        Route::prefix('role')
            ->controller(RoleController::class)
            ->group(function () {
                Route::get('/list', 'list')->name('admin.role.list');
                Route::get('/create', 'create')->name('admin.role.create');
                Route::post('/store', 'store')->name('admin.role.store');
                Route::get('/edit/{role}', 'edit')->name('admin.role.edit');
                Route::post('/update/{role}', 'update')->name('admin.role.update');
                Route::get('/delete/{role}', 'delete')->name('admin.role.delete');
            });
    });
});

// Storage download
Route::get('/storage/{type}/{SID}', [StorageController::class, 'download'])
    ->withoutMiddleware(['csrf', 'cookies', 'encryption'])
    ->middleware([
        Download::class,
    ])
    ->name('storage.download');


// IPG
Route::controller(IpgController::class)->group(function () {
    Route::match(['GET', 'POST'], '/ipg/verify/{payment_uuid}', 'verify')
        ->name('ipgVerify')
        ->withoutMiddleware([VerifyCsrfToken::class]);

    Route::get('/ipg/{token}', 'index')
        ->whereAlphaNumeric('token')
        ->name('ipg');
});
