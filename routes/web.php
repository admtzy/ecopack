<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DppController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\RewardController;
use App\Http\Controllers\PartnershipController;


/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');


/*
|--------------------------------------------------------------------------
| Login dan Register
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        AuthController::class,
        'showLogin',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.post');

    Route::get('/register', [
        AuthController::class,
        'showRegister',
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register',
    ])->name('register.post');

});


/*
|--------------------------------------------------------------------------
| Digital Product Passport Publik
|--------------------------------------------------------------------------
| Halaman ini dapat dibuka tanpa login untuk melihat informasi produk.
*/

Route::get('/dpp/qr/{token}', [
    DppController::class,
    'showQr',
])->name('dpp.show');


/*
|--------------------------------------------------------------------------
| Fitur yang Memerlukan Login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Logout dan Dashboard
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ])->name('logout');

    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Fitur User
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:user')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Scan QR Produk
        |--------------------------------------------------------------------------
        */

        // Halaman kamera untuk scan QR.
        Route::get('/scan-camera', [
            ScanController::class,
            'camera',
        ])->name('scan.camera');

        // Membuka halaman berdasarkan token QR.
        Route::get('/scan/{token}', [
            ScanController::class,
            'open',
        ])->name('scan.open');

        // Memproses scan dan pemberian poin.
        Route::post('/scan/{token}', [
            ScanController::class,
            'scan',
        ])->name('scan.process');


        /*
        |--------------------------------------------------------------------------
        | Katalog dan Penukaran Hadiah
        |--------------------------------------------------------------------------
        */

        Route::get('/rewards', [
            RewardController::class,
            'index',
        ])->name('rewards.index');

        Route::post('/rewards/{reward}/redeem', [
            RewardController::class,
            'redeem',
        ])->name('rewards.redeem');

        Route::get('/my-redemptions', [
            RewardController::class,
            'history',
        ])->name('rewards.history');

    });


    /*
    |--------------------------------------------------------------------------
    | Fitur Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Pengelolaan Digital Product Passport
        |--------------------------------------------------------------------------
        */

        Route::resource('dpp', DppController::class)
            ->except(['show']);

        // Form untuk membuat QR produk.
        Route::get('/dpp/{dpp}/generate-qr', [
            DppController::class,
            'createQr',
        ])->name('dpp.qr.create');

        // Menyimpan QR produk.
        Route::post('/dpp/{dpp}/generate-qr', [
            DppController::class,
            'storeQr',
        ])->name('dpp.qr.store');

        // Mengunduh QR produk dalam ZIP.
        Route::get('/dpp/{dpp}/download-qr-zip', [
            DppController::class,
            'downloadQrZip',
        ])->name('dpp.qr.download');


        /*
        |--------------------------------------------------------------------------
        | Pengelolaan Hadiah
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/rewards', [
            RewardController::class,
            'adminIndex',
        ])->name('admin.rewards.index');

        Route::get('/admin/rewards/create', [
            RewardController::class,
            'create',
        ])->name('admin.rewards.create');

        Route::post('/admin/rewards', [
            RewardController::class,
            'store',
        ])->name('admin.rewards.store');

        Route::get('/admin/rewards/{reward}/edit', [
            RewardController::class,
            'edit',
        ])->name('admin.rewards.edit');

        Route::put('/admin/rewards/{reward}', [
            RewardController::class,
            'update',
        ])->name('admin.rewards.update');

        Route::delete('/admin/rewards/{reward}', [
            RewardController::class,
            'destroy',
        ])->name('admin.rewards.destroy');


        /*
        |--------------------------------------------------------------------------
        | Pengelolaan Penukaran Hadiah
        |--------------------------------------------------------------------------
        */

        Route::get('/redemptions', [
            RewardController::class,
            'redemptions',
        ])->name('admin.redemptions');

        Route::patch('/redemptions/{redemption}', [
            RewardController::class,
            'updateStatus',
        ])->name('admin.redemptions.update');


        /*
        |--------------------------------------------------------------------------
        | Pengelolaan Kemitraan oleh Admin
        |--------------------------------------------------------------------------
        */

        Route::get('/partnerships', [
            PartnershipController::class,
            'index',
        ])->name('partnerships.index');

        Route::patch('/partnerships/{partnership}', [
            PartnershipController::class,
            'updateStatus',
        ])->name('partnerships.update');

    });


    /*
    |--------------------------------------------------------------------------
    | Fitur Mitra
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:mitra')->group(function () {

        // Form pengajuan kemitraan.
        Route::get('/partnerships/create', [
            PartnershipController::class,
            'create',
        ])->name('partnerships.create');

        // Menyimpan pengajuan kemitraan.
        Route::post('/partnerships', [
            PartnershipController::class,
            'store',
        ])->name('partnerships.store');

    });

});