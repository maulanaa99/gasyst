<?php

namespace App\Http\Controllers;

use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    public function index()
    {
        $mobil = Mobil::all();
        return view('mobil.mobil-index', compact('mobil'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mobil' => 'required',
            'plat_no' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
        ]);

        $mobil = new Mobil();
        $mobil->nama_mobil = $request->nama_mobil;
        $mobil->plat_no = $request->plat_no;

        if ($request->hasFile('car_image')) {
            $imageName = time().'.'.$request->car_image->extension();
            $request->file('car_image')->move(public_path('storage/mobil'), $imageName);
            $mobil->car_image = 'mobil/' . $imageName;
        }

        $mobil->status = $request->status;
        $mobil->save();

        return redirect()->route('mobil.index')->with('success', 'Data mobil berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $mobil = Mobil::find($id);

        // Hapus file gambar jika ada
        if ($mobil->car_image) {
            $oldImagePath = public_path('storage/' . $mobil->car_image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        $mobil->delete();

        return redirect()->route('mobil.index')->with('success', 'Data mobil berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_mobil' => 'required',
            'plat_no' => 'required',
            'car_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required',
        ]);

        $mobil = Mobil::find($id);
        $mobil->nama_mobil = $request->nama_mobil;
        $mobil->plat_no = $request->plat_no;

        if ($request->hasFile('car_image')) {
            // Hapus file lama jika ada
            if ($mobil->car_image) {
                $oldImagePath = public_path('storage/' . $mobil->car_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time().'.'.$request->car_image->extension();
            $request->file('car_image')->move(public_path('storage/mobil'), $imageName);
            $mobil->car_image = 'mobil/' . $imageName;
        }

        $mobil->status = $request->status;
        $mobil->save();

        return redirect()->route('mobil.index')->with('success', 'Data mobil berhasil diupdate');
    }
}
