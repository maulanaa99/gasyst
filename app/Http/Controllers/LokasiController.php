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
                'kode_lokasi' => 'required|string|max:255|unique:lokasi',
                'nama_lokasi' => 'required|string|max:255',
                'alamat' => 'required|string',
            ]);

            // Buat lokasi baru
            $lokasi = Lokasi::create($validated);

            if ($lokasi) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lokasi berhasil ditambahkan',
                        'data' => $lokasi
                    ]);
                }
                return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan');
            } else {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menambahkan lokasi'
                    ], 422);
                }
                return redirect()->back()->with('error', 'Gagal menambahkan lokasi');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error saat menambah lokasi: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error saat menambah lokasi: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan lokasi: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menambahkan lokasi: ' . $e->getMessage());
        }
    }
}
