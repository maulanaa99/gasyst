<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananDriverController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratJalanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Routes for admin
    Route::group(['middleware' => ['role:superadmin,admin,user']], function () {
        //Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        //Mobil
        Route::get('/mobil', [MobilController::class, 'index'])->name('mobil.index');
        Route::post('/mobil', [MobilController::class, 'store'])->name('mobil.store');
        Route::put('/mobil/{id}', [MobilController::class, 'update'])->name('mobil.update');
        Route::delete('/mobil/{id}', [MobilController::class, 'destroy'])->name('mobil.destroy');

        //Driver
        Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
        Route::get('/driver/getDriver', [DriverController::class, 'getDriver'])->name('driver.getDriver');
        Route::get('/driver/{id}/edit', [DriverController::class, 'edit'])->name('driver.edit');
        Route::put('/driver/{id}', [DriverController::class, 'update'])->name('driver.update');
        Route::post('/driver', [DriverController::class, 'store'])->name('driver.store');
        Route::delete('/driver/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');

        //Surat Jalan
        Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat-jalan.index');
        Route::post('/surat-jalan', [SuratJalanController::class, 'store'])->name('surat-jalan.store');
        Route::put('/surat-jalan/{id}', [SuratJalanController::class, 'update'])->name('surat-jalan.update');
        Route::delete('/surat-jalan/{id}', [SuratJalanController::class, 'destroy'])->name('surat-jalan.destroy');
    });

    // Routes for superadmin
    Route::group(['middleware' => ['role:superadmin']], function () {

    });

    // Routes for regular users
    Route::group(['middleware' => ['role:user']], function () {

    });

    // Routes for security
    Route::group(['middleware' => ['role:security']], function () {

    });

});

require __DIR__.'/auth.php';
