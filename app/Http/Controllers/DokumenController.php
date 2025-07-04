<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use Illuminate\Http\Request;

class DokumenController extends Controller
{
    public function index()
    {
        $dokumens = Dokumen::all();
        return view('dokumen.dokumen-index', compact('dokumens'));
    }
    public function add()
    {
        return view('dokumen.dokumen-add');
    }
}
