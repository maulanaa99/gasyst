<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::all();
        return view('lokasi.index', compact('lokasi'));
    }

    public function store(Request $request)
    {
        try {
            // Debug untuk melihat data yang diterima
            Log::info('Data yang diterima:', $request->all());

            // Validasi input
            $validated = $request->validate([
                'nama_lokasi' => 'required|string|max:255',
                'alamat' => 'required|string',
            ]);

            // Buat lokasi baru
            $lokasi = Lokasi::create($validated);

            if ($lokasi) {
                return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan');
            } else {
                return redirect()->back()->with('error', 'Gagal menambahkan lokasi');
            }
        } catch (\Exception $e) {
            Log::error('Error saat menambah lokasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan lokasi: ' . $e->getMessage());
        }
    }
}
