<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
    public function getPemesananMobilData($period)
    {
        $data = [];
        $labels = [];
        $values = [];

        // Handle filter bulan spesifik (bulan-1 sampai bulan-12)
        if (preg_match('/^bulan-(\d+)$/', $period, $matches)) {
            $month = (int)$matches[1];
            if ($month >= 1 && $month <= 12) {
                $startDate = Carbon::now()->month($month)->startOfMonth();
                $endDate = Carbon::now()->month($month)->endOfMonth();
                $format = 'd M';
            } else {
                return response()->json(['error' => 'Invalid month'], 400);
            }
        } else {
            return response()->json(['error' => 'Invalid period'], 400);
        }

        // Hitung total pemesanan minggu ini
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();
        $currentWeekTotal = SuratJalan::whereBetween('tanggal', [$currentWeekStart, $currentWeekEnd])->count();

        // Hitung total pemesanan minggu lalu
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();
        $lastWeekTotal = SuratJalan::whereBetween('tanggal', [$lastWeekStart, $lastWeekEnd])->count();

        // Hitung persentase perubahan
        $percentageChange = 0;
        if ($lastWeekTotal > 0) {
            $percentageChange = (($currentWeekTotal - $lastWeekTotal) / $lastWeekTotal) * 100;
        } elseif ($currentWeekTotal > 0) {
            $percentageChange = 100; // Jika minggu lalu 0 dan minggu ini ada pemesanan
        }

        $pemesanan = SuratJalan::whereBetween('tanggal', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        foreach ($pemesanan as $item) {
            $labels[] = Carbon::parse($item->date)->format($format);
            $values[] = $item->total;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'percentageChange' => round($percentageChange, 1)
        ]);
    }

    public function getDriverTripsData($period)
    {
        try {
            $startDate = null;
            $endDate = null;

            // Set tanggal berdasarkan period
            switch ($period) {
                case 'today':
                    $startDate = Carbon::now()->startOfDay();
                    $endDate = Carbon::now()->endOfDay();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                default:
                    return response()->json(['error' => 'Invalid period'], 400);
            }

            // Ambil semua driver
            $drivers = Driver::all();
            $labels = [];
            $values = [];

            // Hitung total perjalanan untuk setiap driver
            foreach ($drivers as $driver) {
                $totalTrips = SuratJalan::where('id_driver', $driver->id)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->count();

                $labels[] = $driver->nama_driver;
                $values[] = $totalTrips;
            }

            return response()->json([
                'labels' => $labels,
                'values' => $values
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getDriverTripsData: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Terjadi kesalahan saat mengambil data perjalanan driver'
            ], 500);
        }
    }
}
