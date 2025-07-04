<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Driver;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SuratJalanController extends Controller
{
    public function index(Request $request)
    {
        $query = SuratJalan::query();

        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } else {
            // Default filter hari ini jika tidak ada parameter
            $today = date('Y-m-d');
            $query->whereDate('tanggal', $today);
        }

        // Filter berdasarkan role user
        if (Auth::user()->role === 'security') {
            // Jika user adalah security, hanya tampilkan data yang sudah di-acknowledge oleh HRGA
            $query->whereNotNull('acknowledged_by');
        }

        $suratJalan = $query->with(['driver', 'karyawans', 'lokasis'])->get();
        $karyawan = Karyawan::all();
        $driver = Driver::all();
        $departemen = Departemen::all();
        $lokasi = Lokasi::all();
        return view('pemesanan_mobil.pemesanan_mobil', compact('suratJalan', 'karyawan', 'driver', 'departemen', 'lokasi'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Incoming request data:', $request->all());

            // Log khusus untuk id_departemen
            Log::info('ID Departemen:', [
                'value' => $request->id_departemen,
                'jenis_pemesanan' => $request->jenis_pemesanan
            ]);

            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date',
                'no_surat_jalan' => 'required|string',
                'jam_berangkat' => 'required',
                'jam_kembali' => 'required',
                'driver_id' => 'nullable|exists:driver,id',
                'keterangan' => 'nullable|string',
                'jenis_pemesanan' => 'required|in:Driver Only,Karyawan',
                'karyawan_id' => 'required_if:jenis_pemesanan,Karyawan|array',
                'karyawan_id.*' => 'exists:karyawan,id',
                'lokasi_id' => 'required|array',
                'lokasi_id.*' => 'exists:lokasi,id',
                'id_departemen' => 'required_if:jenis_pemesanan,Driver Only|exists:departemen,id',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed:', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Buat surat jalan baru
                $suratJalan = new SuratJalan();
                $suratJalan->no_surat_jalan = $request->no_surat_jalan;
                $suratJalan->tanggal = $request->tanggal;
                $suratJalan->jam_berangkat = $request->jam_berangkat;
                $suratJalan->jam_kembali = $request->jam_kembali;
                $suratJalan->id_driver = $request->driver_id;
                $suratJalan->keterangan = $request->keterangan;
                $suratJalan->jenis_pemesanan = $request->jenis_pemesanan;
                $suratJalan->issued_by = Auth::id();
                $suratJalan->issued_at = now();
                $suratJalan->status = 'Dipesan';
                $suratJalan->save();

                // Simpan detail surat jalan
                if ($request->jenis_pemesanan === 'Karyawan') {
                    // Untuk pemesanan karyawan, ambil departemen dari karyawan
                    foreach ($request->karyawan_id as $idKaryawan) {
                        $karyawan = Karyawan::findOrFail($idKaryawan);

                        if (!$karyawan->departemen) {
                            throw new \Exception('Karyawan dengan ID ' . $idKaryawan . ' tidak memiliki departemen');
                        }

                        foreach ($request->lokasi_id as $idLokasi) {
                            $suratJalanDetail = new SuratJalanDetail();
                            $suratJalanDetail->id_surat_jalan = $suratJalan->id;
                            $suratJalanDetail->id_karyawan = $idKaryawan;
                            $suratJalanDetail->id_departemen = $karyawan->departemen->id;
                            $suratJalanDetail->id_lokasi = $idLokasi;
                            $suratJalanDetail->save();
                        }
                    }
                } elseif ($request->jenis_pemesanan === 'Driver Only') {
                    // Untuk Driver Only, buat satu detail dengan departemen
                    if (!$request->id_departemen) {
                        throw new \Exception('Departemen harus diisi untuk pemesanan Driver Only');
                    }

                    foreach ($request->lokasi_id as $idLokasi) {
                        Log::info('Menyimpan detail untuk Driver Only:', [
                            'id_departemen' => $request->id_departemen,
                            'id_lokasi' => $idLokasi
                        ]);

                        $suratJalanDetail = new SuratJalanDetail();
                        $suratJalanDetail->id_surat_jalan = $suratJalan->id;
                        $suratJalanDetail->id_departemen = $request->id_departemen;
                        $suratJalanDetail->id_lokasi = $idLokasi;
                        $suratJalanDetail->save();
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Surat jalan berhasil dibuat',
                    'data' => $suratJalan
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error saat menyimpan ke database: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan ke database: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'karyawan_id' => 'nullable|array',
                'karyawan_id.*' => 'exists:karyawan,id',
                'id_departemen' => 'nullable|exists:departemen,id',
                'jam_berangkat' => 'nullable',
                'jam_kembali' => 'nullable',
                'id_driver' => 'nullable|exists:driver,id',
                'keterangan' => 'required',
                'lokasi_id' => 'required|array',
                'lokasi_id.*' => 'exists:lokasi,id',
                'jenis_pemesanan' => 'required|in:Karyawan,Driver Only'
            ]);

            DB::beginTransaction();

            try {
                $suratJalan = SuratJalan::findOrFail($id);

                // Update hanya field yang diinput, field lain tetap
                $suratJalan->update([
                    'tanggal' => $request->filled('tanggal') ? $request->tanggal : $suratJalan->tanggal,
                    'jam_berangkat' => $request->filled('jam_berangkat') ? $request->jam_berangkat : $suratJalan->jam_berangkat,
                    'jam_kembali' => $request->filled('jam_kembali') ? $request->jam_kembali : $suratJalan->jam_kembali,
                    'id_driver' => $request->filled('id_driver') ? $request->id_driver : $suratJalan->id_driver,
                    'keterangan' => $request->filled('keterangan') ? $request->keterangan : $suratJalan->keterangan,
                    'jenis_pemesanan' => $request->filled('jenis_pemesanan') ? $request->jenis_pemesanan : $suratJalan->jenis_pemesanan,
                ]);

                // Update detail surat jalan hanya jika ada perubahan pada karyawan/lokasi/departemen
                $shouldUpdateDetail = false;
                if ($request->jenis_pemesanan === 'Karyawan' && $request->has('karyawan_id')) {
                    $shouldUpdateDetail = true;
                } elseif ($request->jenis_pemesanan === 'Driver Only' && $request->has('id_departemen')) {
                    $shouldUpdateDetail = true;
                }
                if ($shouldUpdateDetail) {
                    // Hapus detail yang ada
                    $suratJalan->suratJalanDetail()->delete();

                    // Buat detail baru berdasarkan jenis pemesanan
                    if ($request->jenis_pemesanan === 'Karyawan' && $request->has('karyawan_id')) {
                        foreach ($request->karyawan_id as $idKaryawan) {
                            $karyawan = Karyawan::findOrFail($idKaryawan);
                            if (!$karyawan->departemen) {
                                throw new \Exception('Karyawan dengan ID ' . $idKaryawan . ' tidak memiliki departemen');
                            }
                            foreach ($request->lokasi_id as $idLokasi) {
                                $suratJalanDetail = new SuratJalanDetail();
                                $suratJalanDetail->id_surat_jalan = $suratJalan->id;
                                $suratJalanDetail->id_karyawan = $idKaryawan;
                                $suratJalanDetail->id_departemen = $karyawan->departemen->id;
                                $suratJalanDetail->id_lokasi = $idLokasi;
                                $suratJalanDetail->save();
                            }
                        }
                    } elseif ($request->jenis_pemesanan === 'Driver Only') {
                        if (!$request->id_departemen) {
                            throw new \Exception('Departemen harus diisi untuk pemesanan Driver Only');
                        }
                        foreach ($request->lokasi_id as $idLokasi) {
                            $suratJalanDetail = new SuratJalanDetail();
                            $suratJalanDetail->id_surat_jalan = $suratJalan->id;
                            $suratJalanDetail->id_departemen = $request->id_departemen;
                            $suratJalanDetail->id_lokasi = $idLokasi;
                            $suratJalanDetail->save();
                        }
                    }
                }

                DB::commit();

                return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan berhasil diubah');
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error saat update surat jalan: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Update Surat Jalan Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
                'id' => $id
            ]);
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
            if (!in_array($user->role, ['security', 'hrga'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            // Validasi request
            $request->validate([
                'jam_kembali' => 'required|date_format:H:i'
            ]);

            $suratJalan = SuratJalan::findOrFail($id);

            $suratJalan->jam_kembali = $request->jam_kembali;
            $suratJalan->status = 'Selesai';
            $suratJalan->save();

            // Update status driver menjadi Tersedia
            if ($suratJalan->id_driver) {
                $driver = Driver::find($suratJalan->id_driver);
                if ($driver) {
                    $driver->status = 'Tersedia';
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

            if (!in_array($user->role, ['security', 'hrga'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $request->validate([
                'jam_berangkat' => 'required|date_format:H:i'
            ]);

            $suratJalan = SuratJalan::findOrFail($id);

            // Validasi untuk Driver Only tanpa driver
            if ($suratJalan->jenis_pemesanan === 'Driver Only' && is_null($suratJalan->id_driver)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan menghubungi HRGA untuk mengisi Driver yang Bertugas!'
                ], 400);
            }

            $suratJalan->jam_berangkat = $request->jam_berangkat;
            $suratJalan->status = 'Dalam Perjalanan';
            $suratJalan->save();

            // Update status driver menjadi Dalam Perjalanan
            if ($suratJalan->id_driver) {
                $driver = Driver::find($suratJalan->id_driver);
                if ($driver) {
                    $driver->status = 'Dalam Perjalanan';
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
        $suratJalan = SuratJalan::findOrFail($id);



        // Log data untuk debugging
        Log::info('Print Surat Jalan Data', [
            'surat_jalan_id' => $suratJalan->id,
            'no_surat_jalan' => $suratJalan->no_surat_jalan,
            'issued_by' => $suratJalan->issued_by,
            'issued_at' => $suratJalan->issued_at,
            'approve_by' => $suratJalan->approve_by,
            'approve_at' => $suratJalan->approve_at,
            'acknowledged_by' => $suratJalan->acknowledged_by,
            'acknowledged_at' => $suratJalan->acknowledged_at,
            'checked_by' => $suratJalan->checked_by,
            'checked_at' => $suratJalan->checked_at,
        ]);

        return view('pemesanan_mobil.print_surat_jalan', compact('suratJalan'));
    }

    public function checkSecurity($id)
    {
        try {
            $suratJalan = SuratJalan::findOrFail($id);

            // Update status approval
            $suratJalan->update([
                'checked_by' => Auth::id(),
                'checked_at' => now(),
            ]);

            // Log the data for debugging
            Log::info('Surat Jalan Checked', [
                'id' => $suratJalan->id,
                'checked_by' => $suratJalan->checked_by,
                'checked_at' => $suratJalan->checked_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat jalan berhasil dicek'
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking Surat Jalan', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveManager($id)
    {
        try {
            $user = Auth::user();
            $suratJalan = SuratJalan::with(['departemen', 'suratJalanDetail.karyawan.departemen'])->findOrFail($id);

            // Cek apakah user adalah manager
            if ($user->role !== 'manager') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menyetujui surat jalan'
                ], 403);
            }

            // Cek apakah surat jalan sudah disetujui
            if ($suratJalan->approve_by) {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat jalan ini sudah disetujui'
                ], 400);
            }

            // Cek apakah user adalah manager dari departemen terkait
            $isManagerOfRelatedDepartment = false;

            if ($suratJalan->jenis_pemesanan === 'Driver Only') {
                // Untuk Driver Only, cek departemen dari surat jalan
                $isManagerOfRelatedDepartment = $suratJalan->departemen &&
                    $suratJalan->departemen->manager_id === $user->id;
            } else {
                // Untuk pemesanan karyawan, cek departemen dari karyawan yang terlibat
                $isManagerOfRelatedDepartment = $suratJalan->suratJalanDetail->some(function ($detail) use ($user) {
                    return $detail->karyawan &&
                        $detail->karyawan->departemen &&
                        $detail->karyawan->departemen->manager_id === $user->id;
                });
            }

            if (!$isManagerOfRelatedDepartment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda bukan manager dari departemen terkait'
                ], 403);
            }

            DB::beginTransaction();
            try {
                // Update status approval di surat_jalan
                $suratJalan->approve_by = $user->id;
                $suratJalan->approve_at = now();
                $suratJalan->save();

                DB::commit();

                Log::info('Surat Jalan Manager Approval', [
                    'id' => $suratJalan->id,
                    'approve_by' => $suratJalan->approve_by,
                    'approve_at' => $suratJalan->approve_at
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Surat jalan berhasil disetujui oleh manager'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error in approveManager transaction: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in approveManager: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyetujui surat jalan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveHrga($id)
    {
        try {
            $user = Auth::user();
            $suratJalan = SuratJalan::findOrFail($id);

            // Cek apakah user adalah HRGA
            if ($user->role !== 'hrga') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melakukan konfirmasi ini'
                ], 403);
            }

            // Cek apakah surat jalan sudah dikonfirmasi HRGA
            if ($suratJalan->acknowledged_by) {
                return response()->json([
                    'success' => false,
                    'message' => 'Surat jalan ini sudah dikonfirmasi oleh HRGA'
                ], 400);
            }

            DB::beginTransaction();
            try {
                // Update status konfirmasi di surat_jalan
                $suratJalan->acknowledged_by = $user->id;
                $suratJalan->acknowledged_at = now();
                $suratJalan->save();

                DB::commit();

                Log::info('Surat Jalan HRGA Approval', [
                    'id' => $suratJalan->id,
                    'acknowledged_by' => $suratJalan->acknowledged_by,
                    'acknowledged_at' => $suratJalan->acknowledged_at
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Surat jalan berhasil dikonfirmasi oleh HRGA'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error in approveHrga transaction: ' . $e->getMessage());
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error in approveHrga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengkonfirmasi surat jalan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getNextNumber(Request $request)
    {
        try {
            $tanggal = $request->get('tanggal', date('Y-m-d'));
            $count = SuratJalan::whereDate('tanggal', $tanggal)->count();
            $nextNumber = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $no_surat_jalan = 'SJ-' . date('Ymd', strtotime($tanggal)) . '-' . $nextNumber;

            return response()->json([
                'success' => true,
                'no_surat_jalan' => $no_surat_jalan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
