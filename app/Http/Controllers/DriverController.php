<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Karyawan;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DriverController extends Controller
{
    public function index()
    {
        $driver = Driver::all();
        $mobils = Mobil::all();
        $karyawans = Karyawan::all();
        return view('driver.driver-index', compact('driver', 'mobils', 'karyawans'));
    }

    public function getDriver()
    {
        $driver = Driver::all();
        $mobils = Mobil::all();
        return view('driver.driver-index', compact('driver', 'mobils'));
    }

    public function edit($id)
    {
        $driver = Driver::find($id);
        $mobils = Mobil::all();
        return view('driver.driver-index', compact('driver', 'mobils'));
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('Memulai proses update driver dengan ID: ' . $id);
            \Log::info('Data request:', $request->all());

            $request->validate([
                'nama_driver' => 'required',
                'outsourching' => 'required',
                'driver_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required',
                'id_mobil' => 'required',
                'id_karyawan' => 'required',
            ]);

            $driver = Driver::find($id);
            \Log::info('Data driver sebelum update:', $driver->toArray());

            if ($request->hasFile('driver_image')) {
                \Log::info('File driver_image ditemukan, memproses upload');
                // Hapus gambar lama jika ada
                if ($driver->driver_image) {
                    $oldImagePath = storage_path('app/public/' . $driver->driver_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                        \Log::info('Gambar lama berhasil dihapus: ' . $oldImagePath);
                    }
                }

                $imageName = time() . '_' . str_replace(' ', '_', $driver->nama_driver) . '.' . $request->driver_image->extension();
                $path = $request->file('driver_image')->storeAs('driver', $imageName, 'public');
                $driver->driver_image = $path;
                \Log::info('Gambar baru berhasil diupload: ' . $driver->driver_image);
            }

            $driver->nama_driver = $request->nama_driver;
            $driver->outsourching = $request->outsourching;
            $driver->status = $request->status;
            $driver->id_mobil = $request->id_mobil;
            $driver->id_karyawan = $request->id_karyawan;
            $driver->save();

            \Log::info('Data driver berhasil diupdate:', $driver->toArray());
            return redirect()->route('driver.index')->with('success', 'Data driver berhasil diupdate');
        } catch (\Exception $e) {
            \Log::error('Error saat update driver: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('driver.index')->with('error', 'Terjadi kesalahan saat mengupdate data driver: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_driver' => 'required',
            'outsourching' => 'required',
            'driver_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
            'id_mobil' => 'required',
            'id_karyawan' => 'required',
        ]);

        if ($request->hasFile('driver_image')) {
            $imageName = time() . '_' . str_replace(' ', '_', $request->nama_driver) . '.' . $request->driver_image->extension();
            $path = $request->file('driver_image')->storeAs('driver', $imageName, 'public');
        }

        Driver::create([
            'nama_driver' => $request->nama_driver,
            'outsourching' => $request->outsourching,
            'driver_image' => $path,
            'status' => $request->status,
            'id_mobil' => $request->id_mobil,
            'id_karyawan' => $request->id_karyawan,
        ]);

        return redirect()->route('driver.index')->with('success', 'Data driver berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $driver = Driver::find($id);
        $driver->delete();
        return redirect()->route('driver.index')->with('success', 'Data driver berhasil dihapus');
    }
}
