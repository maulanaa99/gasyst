<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DriverController extends Controller
{
    public function index()
    {
        $driver = Driver::all();
        $mobils = Mobil::all();
        return view('driver.driver-index', compact('driver', 'mobils'));
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
        $request->validate([
            'nama_driver' => 'required',
            'outsourching' => 'required',
            'rute' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
            'id_mobil' => 'required',
            'user' => 'required',
        ]);

        $driver = Driver::find($id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($driver->image) {
                $oldImagePath = public_path('storage/' . $driver->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time() . $driver->nama_driver . '.' . $request->image->extension();
            $request->file('image')->move(public_path('storage/driver'), $imageName);
            $driver->image = 'driver/' . $imageName;
        }

        $driver->nama_driver = $request->nama_driver;
        $driver->outsourching = $request->outsourching;
        $driver->rute = $request->rute;
        $driver->status = $request->status;
        $driver->id_mobil = $request->id_mobil;
        $driver->user = $request->user;
        $driver->save();

        return redirect()->route('driver.index')->with('success', 'Data driver berhasil diupdate');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_driver' => 'required',
            'outsourching' => 'required',
            'rute' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
            'id_mobil' => 'required',
            'user' => 'required',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->file('image')->move(public_path('storage/driver'), $imageName);
            $imagePath = 'driver/' . $imageName;
        }

        Driver::create([
            'nama_driver' => $request->nama_driver,
            'outsourching' => $request->outsourching,
            'rute' => $request->rute,
            'image' => $imagePath,
            'status' => $request->status,
            'id_mobil' => $request->id_mobil,
            'user' => $request->user,
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
