@extends('layout.master')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
@endphp

@push('page-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row card-header mx-0 px-2 py-0">
            @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            </script>
            @endif
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                <h5 class="card-title mb-0 text-md-start text-center">List Pemesanan Mobil</h5>
            </div>
            <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto mt-0">
                @if(Auth::user()->role !== 'superadmin' && Auth::user()->role !== 'security')
                <div class="alert alert-info mb-0">
                    Menampilkan data untuk PIC: {{ Auth::user()->name }}
                </div>
                @endif
                @if(Auth::user()->role !== 'security')
                <div class="dt-buttons btn-group flex-wrap">
                    <div class="btn-group">
                        <button class="btn btn-sm buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                            tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                            aria-expanded="false">
                            <span>
                                <span class="d-flex align-items-center gap-2">
                                    <i class="icon-base ri ri-external-link-line icon-18px"></i>
                                    <span class="d-none d-sm-inline-block">Export</span>
                                </span>
                            </span>
                        </button>
                    </div>
                    <button class="btn btn-sm btn-success me-2" id="selectAllBtn">
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-checkbox-multiple-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Select All</span>
                        </span>
                    </button>
                    <button class="btn btn-sm btn-danger me-2" id="deselectAllBtn">
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-checkbox-multiple-blank-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Deselect All</span>
                        </span>
                    </button>
                    <button class="btn btn-sm btn-danger me-2" id="deleteSelectedBtn" style="display: none;">
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-delete-bin-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Hapus Terpilih</span>
                        </span>
                    </button>
                    <button class="btn btn-sm create-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                        type="button" data-bs-toggle="modal" data-bs-target="#addPemesananMobilModal">
                        <span>
                            <span class="d-flex align-items-center">
                                <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New Record</span>
                            </span>
                        </span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="pemesananMobilTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Tujuan</th>
                        <th>Jam Berangkat</th>
                        <th>Jam Kembali</th>
                        <th>Nama Driver</th>
                        {{-- <th>Status</th> --}}
                        <th>PIC</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suratJalan as $item)
                    <tr data-id="{{ $item->id }}">
                        <td></td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d/m/y', strtotime($item->tanggal))
                            }}</td>
                        <td>
                            @if($item->id_karyawan)
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar me-2"><span
                                            class="avatar-initial rounded-circle bg-label-success">{{
                                            $item->karyawan->nama_karyawan[0]}}</span></div>
                                </div>
                                <div class="d-flex flex-column"><span
                                        class="emp_name text-truncate text-heading fw-medium">{{
                                        $item->karyawan->nama_karyawan}}</span><small class="emp_post text-truncate">{{
                                        $item->karyawan->departemen}}</small></div>
                            </div>
                            @else
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar me-2"><span
                                            class="avatar-initial rounded-circle bg-label-success">DO</span></div>
                                </div>
                                <div class="d-flex flex-column"><span
                                        class="emp_name text-truncate text-heading fw-medium">Driver Only</span>
                                    <small class="emp_post text-truncate">{{
                                        $item->departemen->nama_departemen}}</small>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td class="text-nowrap" style="white-space: nowrap;">{{ $item->lokasi->nama_lokasi ?? '-' }}</td>
                        <td>
                            @if($item->jam_berangkat_aktual)
                            <span class="text-success">Aktual : {{ $item->jam_berangkat_aktual }}</span>
                            @else
                            <span class="text-danger">Est : {{ $item->jam_berangkat }}</span>
                            @if((Auth::user()->role === 'superadmin' || Auth::user()->role === 'security') &&
                            !$item->status_jam_berangkat_aktual)
                            <div class="d-flex align-items-center mt-1">
                                <button type="button" class="btn btn-sm btn-info ms-2"
                                    onclick="setJamBerangkat({{ $item->id }})">
                                    <i class="icon-base ri ri-time-line"></i>
                                </button>
                            </div>
                            @endif
                            @endif
                        </td>
                        <td>
                            @if($item->jam_kembali_aktual)
                            <span class="text-success">Aktual : {{ $item->jam_kembali_aktual }}</span>
                            @else
                            <span class="text-danger">Est : {{ $item->jam_kembali }}</span>
                            @if((Auth::user()->role === 'superadmin' || Auth::user()->role === 'security') &&
                            !$item->status_jam_kembali_aktual)
                            <div class="d-flex align-items-center mt-1">
                                @if($item->jam_berangkat_aktual)
                                <button type="button" class="btn btn-sm btn-info ms-2"
                                    onclick="setJamKembali({{ $item->id }})">
                                    <i class="icon-base ri ri-time-line"></i>
                                </button>
                                @endif
                            </div>
                            @endif
                            @endif
                        </td>
                        <td>{{ $item->driver->nama_driver ?? '-' }}</td>
                        {{-- <td>{{ $item->status }}</td> --}}
                        <td>{{ $item->PIC }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="d-flex gap-2">
                            <button type="button"
                                class="btn btn-icon btn-warning btn-sm btn-edit waves-effect waves-light"
                                data-bs-toggle="modal" data-bs-target="#editPemesananMobilModal{{ $item->id }}">
                                <i class="icon-base ri ri-edit-line icon-18px" style="color: white"></i>
                            </button>
                            <button type="button"
                                class="btn btn-icon btn-danger btn-sm btn-fab demo waves-effect waves-light delete-btn"
                                data-id="{{ $item->id }}">
                                <i class="icon-base ri ri-delete-bin-line icon-18px" style="color: white"></i>
                            </button>
                        </td>
                    </tr>


                    <!-- Modal Edit untuk item ini -->
                    <div class="modal fade" id="editPemesananMobilModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="editPemesananMobilModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editPemesananMobilModalLabel{{ $item->id }}">Edit Data
                                        Pemesanan Mobil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('surat-jalan.update', $item->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group">
                                                    <label class="form-label">Jenis Pemesanan</label>
                                                    <div class="d-flex gap-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="jenis_pemesanan" id="jenisKaryawan{{ $item->id }}"
                                                                value="karyawan" {{ $item->id_karyawan ? 'checked' : ''
                                                            }}>
                                                            <label class="form-check-label"
                                                                for="jenisKaryawan{{ $item->id }}">
                                                                Karyawan
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="jenis_pemesanan"
                                                                id="jenisDriverOnly{{ $item->id }}" value="driver_only"
                                                                {{ !$item->id_karyawan ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="jenisDriverOnly{{ $item->id }}">
                                                                Driver Only
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tanggal{{ $item->id }}"
                                                        class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control" id="tanggal{{ $item->id }}"
                                                        name="tanggal" value="{{ $item->tanggal }}" required>
                                                </div>
                                                <div id="formKaryawan{{ $item->id }}"
                                                    style="display: {{ $item->id_karyawan ? 'block' : 'none' }}">
                                                    <div class="form-group mb-3">
                                                        <label for="id_karyawan{{ $item->id }}" class="form-label">Nama
                                                            Karyawan</label>
                                                        <select class="form-select select2"
                                                            id="id_karyawan{{ $item->id }}" name="id_karyawan">
                                                            <option value="">Pilih Karyawan</option>
                                                            @foreach($karyawan as $k)
                                                            <option value="{{ $k->id }}" {{ $item->id_karyawan == $k->id
                                                                ?
                                                                'selected' : '' }}>
                                                                {{ $k->nik }} | {{ $k->nama_karyawan }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div id="formDriverOnly{{ $item->id }}"
                                                    style="display: {{ !$item->id_karyawan ? 'block' : 'none' }}">
                                                    <div class="form-group mb-3">
                                                        <label for="departemen{{ $item->id }}"
                                                            class="form-label">Departemen</label>
                                                        <select class="form-select select2"
                                                            id="id_departemen{{ $item->id }}" name="id_departemen">
                                                            <option value="">Pilih Departemen</option>
                                                            @foreach($departemen as $d)
                                                            <option value="{{ $d->id }}" {{ $item->id_departemen ==
                                                                $d->id ? 'selected' : '' }}>
                                                                {{ $d->nama_departemen }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="lokasi{{ $item->id }}" class="form-label">Lokasi</label>
                                                    <select class="form-select select2" id="id_lokasi{{ $item->id }}"
                                                        name="id_lokasi">
                                                        <option value="">Pilih Lokasi</option>
                                                        @foreach($lokasi as $l)
                                                        <option value="{{ $l->id }}" {{ $item->id_lokasi == $l->id ?
                                                            'selected' : '' }}>
                                                            {{ $l->nama_lokasi }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="jam_berangkat{{ $item->id }}" class="form-label">Jam
                                                        Berangkat</label>
                                                    <input type="time" class="form-control"
                                                        id="jam_berangkat{{ $item->id }}" name="jam_berangkat"
                                                        value="{{ $item->jam_berangkat }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="jam_kembali{{ $item->id }}" class="form-label">Jam
                                                        Kembali</label>
                                                    <input type="time" class="form-control"
                                                        id="jam_kembali{{ $item->id }}" name="jam_kembali"
                                                        value="{{ $item->jam_kembali }}" required>
                                                </div>
                                                @if(Auth::user()->role === 'superadmin')
                                                <div class="form-group mb-3">
                                                    <label for="id_driver{{ $item->id }}" class="form-label">Nama
                                                        Driver</label>
                                                    <select class="form-select select2" id="id_driver{{ $item->id }}"
                                                        name="id_driver" required>
                                                        <option value="">Pilih Driver</option>
                                                        @foreach($driver as $d)
                                                        @if($d->status === 'Available' || $item->id_driver == $d->id)
                                                        <option value="{{ $d->id }}" {{ $item->id_driver == $d->id ?
                                                            'selected' : '' }}>
                                                            {{ $d->nama_driver }}
                                                        </option>
                                                        @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="keterangan{{ $item->id }}"
                                                    class="form-label">Keterangan</label>
                                                <textarea class="form-control h-px-100" id="keterangan{{ $item->id }}"
                                                    name="keterangan" required>{{ $item->keterangan }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pemesanan Mobil -->
<div class="modal fade" id="addPemesananMobilModal" tabindex="-1" aria-labelledby="addPemesananMobilModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPemesananMobilModalLabel">Tambah Data Pemesanan Mobil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data" id="addPemesananMobilForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label class="form-label">Jenis Pemesanan</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_pemesanan"
                                            id="jenisKaryawan" value="karyawan" checked>
                                        <label class="form-check-label" for="jenisKaryawan">
                                            Karyawan
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="jenis_pemesanan"
                                            id="jenisDriverOnly" value="driver_only">
                                        <label class="form-check-label" for="jenisDriverOnly">
                                            Driver Only
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div id="formKaryawan">
                                <div class="form-group mb-3">
                                    <label for="id_karyawan" class="form-label">Nama Karyawan</label>
                                    <select class="form-select select2" id="id_karyawan" name="id_karyawan">
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($karyawan as $karyawans)
                                        <option value="{{ $karyawans->id }}">{{ $karyawans->nik }} | {{
                                            $karyawans->nama_karyawan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="formDriverOnly" style="display: none;">
                                <div class="form-group mb-3">
                                    <label for="id_departemen" class="form-label">Departemen</label>
                                    <select class="form-select select2" id="id_departemen" name="id_departemen">
                                        <option value="">Pilih Departemen</option>
                                        @foreach($departemen as $departemen)
                                        <option value="{{ $departemen->id }}">{{ $departemen->nama_departemen }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="lokasi" class="form-label">Lokasi</label>
                                <select class="form-select select2" id="id_lokasi" name="id_lokasi">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($lokasi as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="jam_berangkat" class="form-label">Jam Berangkat</label>
                                <input type="time" class="form-control" id="jam_berangkat" name="jam_berangkat"
                                    required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="jam_kembali" class="form-label">Jam Kembali</label>
                                <input type="time" class="form-control" id="jam_kembali" name="jam_kembali" required>
                            </div>

                            @if(Auth::user()->role === 'superadmin')
                            <div class="form-group mb-3">
                                <label for="id_driver" class="form-label">Nama Driver</label>
                                <select class="form-select select2" id="id_driver" name="id_driver" required>
                                    <option value="">Pilih Driver</option>
                                    @foreach($driver as $drivers)
                                    @if($drivers->status === 'Available')
                                    <option value="{{ $drivers->id }}">{{ $drivers->nama_driver }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            @endif


                        </div>
                        <div class="form-group mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control h-px-100" id="keterangan" name="keterangan"
                                required></textarea>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="PIC" name="PIC" value="{{ auth()->user()->name }}"
                        required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="submitSpinner"></span>
                        <span id="submitText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@push('page-script')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
@endpush

@push('after-script')
<script>
    // Fungsi untuk mendapatkan CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    }

    // Fungsi untuk mengisi jam kembali di tabel
    function setJamKembali(id) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const currentTime = `${hours}:${minutes}`;

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mengisi jam kembali aktual dan mengubah status driver menjadi available?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, isi jam kembali',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/surat-jalan/${id}/update-jam-kembali`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        jam_kembali_aktual: currentTime
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Terjadi kesalahan saat mengupdate jam kembali');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Jam kembali berhasil diupdate',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan saat mengupdate jam kembali');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: error.message || 'Terjadi kesalahan saat mengupdate jam kembali'
                    });
                });
            }
        });
    }

    // Fungsi untuk mengisi jam berangkat
    function setJamBerangkat(id) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const currentTime = `${hours}:${minutes}`;

        // Cek apakah id_driver ada
        fetch(`/surat-jalan/${id}/check-driver`, {
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (!data.hasDriver) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silahkan isi driver terlebih dahulu!'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengisi jam berangkat aktual?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, isi jam berangkat',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/surat-jalan/${id}/update-jam-berangkat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({
                            jam_berangkat: currentTime
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Terjadi kesalahan saat mengupdate jam berangkat');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Jam berangkat berhasil diupdate',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat mengupdate jam berangkat');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat mengupdate jam berangkat'
                        });
                    });
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat memeriksa data driver'
            });
        });
    }

    $(document).ready(function() {
        console.log('Document ready');

        // Destroy existing DataTable instance if it exists
        if ($.fn.DataTable.isDataTable('#pemesananMobilTable')) {
            $('#pemesananMobilTable').DataTable().destroy();
        }

        // Initialize DataTable
        const table = $('#pemesananMobilTable').DataTable({
            responsive: true,
            scrollX: true,
            scrollY: '400px',
            scrollCollapse: true,
            fixedColumns: true,
            autoWidth: false,
            select: {
                style: 'multi',
                selector: 'td:first-child',
                className: 'selected'
            },
            columnDefs: [
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0,
                    width: '30px'
                },
                { width: '50px', targets: 1 },  // No
                { width: '100px', targets: 2 }, // Tanggal
                { width: '200px', targets: 3 }, // Nama Karyawan
                { width: '150px', targets: 4 }, // Lokasi
                { width: '120px', targets: 5 }, // Jam Berangkat
                { width: '120px', targets: 6 }, // Jam Kembali
                { width: '150px', targets: 7 }, // Nama Driver
                { width: '100px', targets: 8 }, // PIC
                { width: '150px', targets: 9 }, // Keterangan
                { width: '100px', targets: 10 }  // Action
            ],
            language: {
                select: {
                    rows: {
                        _: "You have selected %d rows",
                        0: "Click a row to select it",
                        1: "Only 1 row selected"
                    }
                },
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });

        // Toggle delete selected button visibility
        table.on('select deselect', function() {
            const selectedRows = table.rows({ selected: true }).count();
            $('#deleteSelectedBtn').toggle(selectedRows > 0);
        });

        // Select All button
        $('#selectAllBtn').on('click', function() {
            table.rows().select();
        });

        // Deselect All button
        $('#deselectAllBtn').on('click', function() {
            table.rows().deselect();
        });

        // Delete selected rows
        $('#deleteSelectedBtn').on('click', function() {
            const selectedRows = table.rows({ selected: true });
            const selectedIds = [];

            // Extract only the IDs from selected rows
            selectedRows.nodes().each(function(row) {
                selectedIds.push($(row).data('id'));
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'No data selected!'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `You will delete ${selectedIds.length} selected data!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch('/surat-jalan/delete-selected', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({
                            ids: selectedIds
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.success) {
                        // Remove selected rows from the table
                        selectedRows.remove().draw();

                        // Hide delete button if no rows are selected
                        if (table.rows({ selected: true }).count() === 0) {
                            $('#deleteSelectedBtn').hide();
                        }

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Selected data has been deleted',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: result.value.message || 'Failed to delete selected data'
                        });
                    }
                }
            });
        });

        // Initialize Select2 for all current and future select elements with class select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Initialize Select2 for dynamic modal dropdowns
        $(document).on('shown.bs.modal', '.modal', function() {
            $(this).find('select').select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this),
                width: '100%'
            });
        });

        // Handle jenis pemesanan radio buttons untuk form tambah
        $('input[name="jenis_pemesanan"]').change(function() {
            if ($(this).val() === 'karyawan') {
                $('#formKaryawan').show();
                $('#formDriverOnly').hide();
                $('#id_karyawan').prop('required', true);
                $('#departemen').prop('required', false);
            } else {
                $('#formKaryawan').hide();
                $('#formDriverOnly').show();
                $('#id_karyawan').prop('required', false);
                $('#departemen').prop('required', true);
            }
        });

        // Handle jenis pemesanan radio buttons untuk form edit
        $(document).on('change', 'input[name="jenis_pemesanan"]', function() {
            const modalId = $(this).closest('.modal').attr('id');
            const itemId = modalId.replace('editPemesananMobilModal', '');

            if ($(this).val() === 'karyawan') {
                $(`#formKaryawan${itemId}`).show();
                $(`#formDriverOnly${itemId}`).hide();
                $(`#id_karyawan${itemId}`).prop('required', true);
                $(`#departemen${itemId}`).prop('required', false);
            } else {
                $(`#formKaryawan${itemId}`).hide();
                $(`#formDriverOnly${itemId}`).show();
                $(`#id_karyawan${itemId}`).prop('required', false);
                $(`#departemen${itemId}`).prop('required', true);
            }
        });

        // SweetAlert2 untuk konfirmasi hapus
        $(document).on('click', '.delete-btn', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Buat form untuk submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/surat-jalan/${id}`;

                    // Tambahkan CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Tambahkan method DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);

                    // Tambahkan form ke body dan submit
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        // Handle form submission untuk mencegah double submit
        $('#addPemesananMobilForm').on('submit', function(e) {
            const submitBtn = $('#submitBtn');
            const submitSpinner = $('#submitSpinner');
            const submitText = $('#submitText');

            // Disable tombol submit
            submitBtn.prop('disabled', true);

            // Tampilkan spinner
            submitSpinner.removeClass('d-none');
            submitText.text('Menyimpan...');

            // Validasi form
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();

                // Reset tombol jika validasi gagal
                submitBtn.prop('disabled', false);
                submitSpinner.addClass('d-none');
                submitText.text('Simpan');
                return;
            }
        });

        // Reset form dan tombol ketika modal ditutup
        $('#addPemesananMobilModal').on('hidden.bs.modal', function() {
            const submitBtn = $('#submitBtn');
            const submitSpinner = $('#submitSpinner');
            const submitText = $('#submitText');

            // Reset form
            $('#addPemesananMobilForm')[0].reset();

            // Reset tombol
            submitBtn.prop('disabled', false);
            submitSpinner.addClass('d-none');
            submitText.text('Simpan');

            // Reset Select2
            $('.select2').val(null).trigger('change');
        });
    });
</script>
@endpush