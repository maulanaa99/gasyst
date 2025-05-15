<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Driver;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuratJalanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'superadmin' || $user->role === 'security') {
            $suratJalan = SuratJalan::all();
        } else {
            $suratJalan = SuratJalan::where('PIC', $user->name)->get();
        }

        $karyawan = Karyawan::all();
        $driver = Driver::all();
        $departemen = Departemen::all();
        $lokasi = Lokasi::all();
        return view('pemesanan_mobil.pemesanan_mobil', compact('suratJalan', 'karyawan', 'driver', 'departemen', 'lokasi'));
    }

    public function store(Request $request)
    {
        $suratJalan = SuratJalan::create($request->all());
        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $suratJalan = SuratJalan::find($id);
        $suratJalan->update($request->all());
        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil diubah');
    }

    public function destroy($id)
    {
        $suratJalan = SuratJalan::find($id);

        // Jika ada driver, update status menjadi Available
        if ($suratJalan->id_driver) {
            $driver = Driver::find($suratJalan->id_driver);
            if ($driver) {
                $driver->status = 'Available';
                $driver->save();
            }
        }

        $suratJalan->delete();
        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil dihapus');
    }

    public function updateJamKembali(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Validasi role
            if ($user->role !== 'superadmin' && $user->role !== 'security') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Validasi request
            $request->validate([
                'jam_kembali_aktual' => 'required|date_format:H:i'
            ]);

            $suratJalan = SuratJalan::findOrFail($id);
            $suratJalan->jam_kembali_aktual = $request->jam_kembali_aktual;
            $suratJalan->status_jam_kembali_aktual = true;
            $suratJalan->save();

            // Update status driver menjadi Available
            if ($suratJalan->id_driver) {
                $driver = Driver::find($suratJalan->id_driver);
                if ($driver) {
                    $driver->status = 'Available';
                    $driver->save();
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengupdate jam kembali',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateJamBerangkat(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Validasi role
            if ($user->role !== 'superadmin' && $user->role !== 'security') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $suratJalan = SuratJalan::findOrFail($id);

            // Validasi request
            $request->validate([
                'jam_berangkat' => 'required|date_format:H:i'
            ]);

            $suratJalan->jam_berangkat_aktual = $request->jam_berangkat;
            $suratJalan->status_jam_berangkat_aktual = true;
            $suratJalan->save();

            // Update status driver menjadi Not Available
            if ($suratJalan->id_driver) {
                $driver = Driver::find($suratJalan->id_driver);
                if ($driver) {
                    $driver->status = 'Not Available';
                    $driver->save();
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan saat mengupdate jam berangkat',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function checkDriver($id)
    {
        $suratJalan = SuratJalan::findOrFail($id);
        return response()->json([
            'hasDriver' => !is_null($suratJalan->id_driver)
        ]);
    }
}
