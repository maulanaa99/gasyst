<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Karyawan;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    public function index()
    {
        $suratJalan = SuratJalan::all();
        $karyawan = Karyawan::all();
        $driver = Driver::all();
        return view('pemesanan_mobil.pemesanan_mobil', compact('suratJalan', 'karyawan', 'driver'));
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
        $suratJalan->delete();
        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil dihapus');
    }
}
