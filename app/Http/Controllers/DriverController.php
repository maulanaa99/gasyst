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
        return response()->json($driver);
    }

    public function update(Request $request, $id)
    {
        $driver = Driver::find($id);
        $driver->update($request->all());
        return redirect()->route('driver.index')->with('success', 'Data driver berhasil diupdate');
    }

    public function store(Request $request)
    {
        Driver::create($request->all());
        return redirect()->route('driver.index')->with('success', 'Data driver berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $driver = Driver::find($id);
        $driver->delete();
        return redirect()->route('driver.index')->with('success', 'Data driver berhasil dihapus');
    }
}
