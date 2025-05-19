<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Driver;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        try {
            // Debug untuk melihat data yang diterima
            Log::info('Data yang diterima:', $request->all());

            // Validasi input
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'karyawan_id' => 'nullable|array',
                'karyawan_id.*' => 'exists:karyawans,id',
                'id_departemen' => 'nullable|exists:departemen,id',
                'jam_berangkat' => 'required',
                'jam_kembali' => 'required',
                'id_driver' => 'required|exists:drivers,id',
                'keterangan' => 'required',
                'PIC' => 'required',
                'lokasi_id' => 'required|array',
                'lokasi_id.*' => 'exists:lokasis,id',
                'jenis_pemesanan' => 'required|in:Karyawan,Driver Only'
            ]);

            // Generate nomor surat jalan
            $no_surat_jalan = 'SJ-' . date('Ymd') . '-' . str_pad(SuratJalan::count() + 1, 4, '0', STR_PAD_LEFT);

            // Buat surat jalan baru
            $suratJalan = SuratJalan::create([
                'tanggal' => $validated['tanggal'],
                'jam_berangkat' => $validated['jam_berangkat'],
                'jam_kembali' => $validated['jam_kembali'],
                'id_driver' => $validated['id_driver'],
                'keterangan' => $validated['keterangan'],
                'PIC' => $validated['PIC'],
                'id_departemen' => $request->jenis_pemesanan === 'Driver Only' ? $validated['id_departemen'] : null,
                'no_surat_jalan' => $no_surat_jalan,
                'status' => 'pending',
                'jenis_pemesanan' => $validated['jenis_pemesanan']
            ]);

            // Simpan relasi ke tabel surat_jalan_detail
            if ($request->jenis_pemesanan === 'Karyawan' && $request->has('karyawan_id')) {
                foreach ($request->karyawan_id as $karyawan_id) {
                    foreach ($request->lokasi_id as $lokasi_id) {
                        DB::table('surat_jalan_detail')->insert([
                            'surat_jalan_id' => $suratJalan->id,
                            'karyawan_id' => $karyawan_id,
                            'lokasi_id' => $lokasi_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            } else {
                // Jika driver only, simpan hanya relasi lokasi
                foreach ($request->lokasi_id as $lokasi_id) {
                    DB::table('surat_jalan_detail')->insert([
                        'surat_jalan_id' => $suratJalan->id,
                        'lokasi_id' => $lokasi_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan surat jalan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan surat jalan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'karyawan_id' => 'nullable|array',
                'karyawan_id.*' => 'exists:karyawans,id',
                'id_departemen' => 'nullable|exists:departemen,id',
                'jam_berangkat' => 'required',
                'jam_kembali' => 'required',
                'id_driver' => 'required|exists:drivers,id',
                'keterangan' => 'required',
                'lokasi_id' => 'required|array',
                'lokasi_id.*' => 'exists:lokasis,id',
                'jenis_pemesanan' => 'required|in:Karyawan,Driver Only'
            ]);

            $suratJalan = SuratJalan::findOrFail($id);

            // Update data surat jalan
            $suratJalan->update([
                'tanggal' => $validated['tanggal'],
                'jam_berangkat' => $validated['jam_berangkat'],
                'jam_kembali' => $validated['jam_kembali'],
                'id_driver' => $validated['id_driver'],
                'keterangan' => $validated['keterangan'],
                'id_departemen' => $validated['id_departemen'],
                'jenis_pemesanan' => $validated['jenis_pemesanan']
            ]);

            // Update relasi karyawan
            if ($request->has('karyawan_id')) {
                $suratJalan->karyawan()->sync($request->karyawan_id);
            }

            // Update relasi lokasi
            if ($request->has('lokasi_id')) {
                $suratJalan->lokasi()->sync($request->lokasi_id);
            }

            return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil diubah');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah surat jalan: ' . $e->getMessage());
        }
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
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
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

            return response()->json([
                'success' => true,
                'message' => 'Jam kembali berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate jam kembali: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateJamBerangkat(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Validasi role
            if ($user->role !== 'superadmin' && $user->role !== 'security') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
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

            return response()->json([
                'success' => true,
                'message' => 'Jam berangkat berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupdate jam berangkat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDriver($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);
            return response()->json([
                'success' => true,
                'hasDriver' => !is_null($suratJalan->id_driver)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa driver: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSelected(Request $request)
    {
        try {
            $ids = $request->input('ids');

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih'
                ], 400);
            }

            // Update status driver menjadi Available untuk setiap surat jalan yang akan dihapus
            $suratJalan = SuratJalan::whereIn('id', $ids)->get();
            foreach ($suratJalan as $sj) {
                if ($sj->id_driver) {
                    $driver = Driver::find($sj->id_driver);
                    if ($driver) {
                        $driver->status = 'Available';
                        $driver->save();
                    }
                }
            }

            // Hapus surat jalan yang dipilih
            SuratJalan::whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data yang dipilih berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $karyawan = Karyawan::all();
        $driver = Driver::all();
        $departemen = Departemen::all();
        $lokasi = Lokasi::all();
        return view('pemesanan_mobil.pemesanan_mobil_add', compact('karyawan', 'driver', 'departemen', 'lokasi'));
    }

    public function print($id)
    {
        $suratJalan = SuratJalan::with(['karyawan', 'departemen', 'lokasi', 'driver', 'approvedBy'])->findOrFail($id);

        // Log data untuk debugging
        Log::info('Print Surat Jalan Data', [
            'surat_jalan_id' => $suratJalan->id,
            'status_approve' => $suratJalan->status_approve,
            'approved_by' => $suratJalan->approved_by,
            'approved_at' => $suratJalan->approved_at,
            'security_user' => $suratJalan->approvedBy ? [
                'id' => $suratJalan->approvedBy->id,
                'name' => $suratJalan->approvedBy->name,
                'signature' => $suratJalan->approvedBy->signature
            ] : null
        ]);

        return view('pemesanan_mobil.print_surat_jalan', compact('suratJalan'));
    }

    public function approve($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);

            // Update status approve
            $suratJalan->status_approve = true;
            $suratJalan->approved_by = Auth::id();
            $suratJalan->approved_at = now();
            $suratJalan->save();

            // Log the data for debugging
            \Log::info('Surat Jalan Approved', [
                'id' => $suratJalan->id,
                'status_approve' => $suratJalan->status_approve,
                'approved_by' => $suratJalan->approved_by,
                'approved_at' => $suratJalan->approved_at
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat jalan berhasil disetujui'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving Surat Jalan', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
