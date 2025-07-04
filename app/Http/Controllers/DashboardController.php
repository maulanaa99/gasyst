<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Mobil;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPemesananMobil = SuratJalan::count();
        $totalDriver = Driver::count();
        $totalMobil = Mobil::count();
        $DriverAvailable = Driver::where('status', 'Available')->count();
        $notAvailableDriver = Driver::where('status', 'Not Available')->count();
        $driver = Driver::with('mobil')->get();
        $mobil = Mobil::all();

        return view('dashboard', compact(
            'totalPemesananMobil',
            'totalDriver',
            'totalMobil',
            'DriverAvailable',
            'notAvailableDriver',
            'driver',
            'mobil'
        ));
    }
}
