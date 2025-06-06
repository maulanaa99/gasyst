<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\PemesananDriverController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\LokasiController;
use App\Models\Driver;
use App\Models\Mobil;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// API Routes for Charts
Route::get('/api/pemesanan-mobil/{period}', [ChartController::class, 'getPemesananMobilData']);
Route::get('/api/driver-trips/{period}', [ChartController::class, 'getDriverTripsData']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $totalPemesananMobil = SuratJalan::count();
        $totalDriver = Driver::count();
        $totalMobil = Mobil::count();
        $DriverAvailable = Driver::where('status', 'Available')->count();
        $notAvailableDriver = Driver::where('status', 'Not Available')->count();
        $driver = Driver::all();
        $mobil = Mobil::all();
        return view('dashboard', compact('totalPemesananMobil', 'totalDriver', 'totalMobil', 'DriverAvailable', 'notAvailableDriver', 'driver', 'mobil'));
    })->name('dashboard');

    // Routes for admin
    Route::group(['middleware' => ['role:superadmin,admin,user,security']], function () {
        //Profile
        Route::get('/profile/index', [ProfileController::class, 'index'])->name('profile.index');
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
        Route::get('/surat-jalan/create', [SuratJalanController::class, 'create'])->name('surat-jalan.create');
        Route::put('/surat-jalan/{id}', [SuratJalanController::class, 'update'])->name('surat-jalan.update');
        Route::delete('/surat-jalan/{id}', [SuratJalanController::class, 'destroy'])->name('surat-jalan.destroy');
        Route::post('/surat-jalan/{id}/update-jam-kembali', [SuratJalanController::class, 'updateJamKembali'])->name('surat-jalan.update-jam-kembali');
        Route::post('/surat-jalan/{id}/update-jam-berangkat', [SuratJalanController::class, 'updateJamBerangkat'])->name('surat-jalan.update-jam-berangkat');
        Route::get('/surat-jalan/{id}/check-driver', [SuratJalanController::class, 'checkDriver'])->name('surat-jalan.check-driver');
        Route::post('/surat-jalan/delete-selected', [SuratJalanController::class, 'deleteSelected'])->name('surat-jalan.delete-selected');
        Route::get('/surat-jalan/{id}/print', [SuratJalanController::class, 'print'])->name('surat-jalan.print');
        Route::post('/surat-jalan/{id}/approve', [SuratJalanController::class, 'approve'])->middleware(['auth', 'role:security'])->name('surat-jalan.approve');

        //Lokasi
        Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
        Route::post('/lokasi', [LokasiController::class, 'store'])->name('lokasi.store');
        Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update');
        Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');

    });

    // Routes for superadmin
    Route::group(['middleware' => ['role:superadmin']], function () {});

    // Routes for regular users
    Route::group(['middleware' => ['role:user']], function () {});

    // Routes for security
    Route::group(['middleware' => ['role:superadmin,security']], function () {
        // Route::get('/surat-jalan', [SuratJalanController::class, 'index'])->name('surat-jalan.index');
    });
});

Route::get('/signature/{filename}', function ($filename) {
    $filename = str_replace('signatures/', '', $filename);
    $path = storage_path('app/private/public/signatures/' . $filename);

    \Illuminate\Support\Facades\Log::info('Signature Request', [
        'original_filename' => $filename,
        'path' => $path,
        'exists' => file_exists($path)
    ]);

    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->name('signature.show')->where('filename', '.*');

Route::get('/security-cap', function () {
    $path = storage_path('app/private/public/signatures/Cap SR.png');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path);
})->name('security.cap');

require __DIR__ . '/auth.php';
