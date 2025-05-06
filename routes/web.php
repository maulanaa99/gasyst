<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/master', function () {
    return view('layout.master');
})->middleware('auth')->name('master');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/driver', [DriverController::class, 'index'])->name('driver.index');
    Route::get('/driver/getDriver', [DriverController::class, 'getDriver'])->name('driver.getDriver');
    Route::get('/driver/{id}/edit', [DriverController::class, 'edit'])->name('driver.edit');
    Route::put('/driver/{id}', [DriverController::class, 'update'])->name('driver.update');
    Route::post('/driver', [DriverController::class, 'store'])->name('driver.store');
    Route::delete('/driver/{id}', [DriverController::class, 'destroy'])->name('driver.destroy');

    Route::get('/mobil', [MobilController::class, 'index'])->name('mobil.index');
    Route::post('/mobil', [MobilController::class, 'store'])->name('mobil.store');
    Route::put('/mobil/{id}', [MobilController::class, 'update'])->name('mobil.update');
    Route::delete('/mobil/{id}', [MobilController::class, 'destroy'])->name('mobil.destroy');
});

require __DIR__.'/auth.php';
