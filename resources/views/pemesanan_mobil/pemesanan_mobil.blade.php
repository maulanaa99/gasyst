@extends('layout.master')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('page-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" />
@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row card-header mx-0 px-2">
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                <h5 class="card-title mb-0 text-md-start text-center">List Pemesanan Mobil</h5>
            </div>
            <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto mt-0">
                <div class="dt-buttons btn-group flex-wrap">
                    <div class="btn-group"><button
                            class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                            tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog"
                            aria-expanded="false"><span><span class="d-flex align-items-center gap-2"><i
                                        class="icon-base ri ri-external-link-line icon-18px"></i> <span
                                        class="d-none d-sm-inline-block">Export</span></span></span></button></div>
                    <button class="btn create-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                        type="button" data-bs-toggle="modal" data-bs-target="#addPemesananMobilModal"><span><span
                                class="d-flex align-items-center"><i
                                    class="icon-base ri ri-add-line icon-18px me-sm-1"></i><span
                                    class="d-none d-sm-inline-block">Add New Record</span></span></span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="pemesananMobilTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Tujuan</th>
                        <th>Jam Berangkat</th>
                        <th>Nama Driver</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suratJalan as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->nama_karyawan }}</td>
                        <td>{{ $item->tujuan }}</td>
                        <td>{{ $item->jam_berangkat }}</td>
                        <td>{{ $item->nama_driver }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#editPemesananMobilModal{{ $item->id }}">
                                Edit
                            </button>
                            <form action="{{ route('surat-jalan.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                            </form>
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
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="tanggal{{ $item->id }}"
                                                        class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control" id="tanggal{{ $item->id }}"
                                                        name="tanggal" value="{{ $item->tanggal }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="id_karyawan{{ $item->id }}" class="form-label">Nama
                                                        Karyawan</label>
                                                    <select class="form-select select2" id="id_karyawan{{ $item->id }}" name="id_karyawan" required>
                                                        @foreach($karyawan as $k)
                                                            <option value="{{ $k->id }}" {{ $item->id_karyawan == $k->id ? 'selected' : '' }}>
                                                                {{ $k->nama_karyawan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="tujuan{{ $item->id }}" class="form-label">Tujuan</label>
                                                    <input type="text" class="form-control" id="tujuan{{ $item->id }}"
                                                        name="tujuan" value="{{ $item->tujuan }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="jam_berangkat{{ $item->id }}" class="form-label">Jam
                                                        Berangkat</label>
                                                    <input type="time" class="form-control"
                                                        id="jam_berangkat{{ $item->id }}" name="jam_berangkat"
                                                        value="{{ $item->jam_berangkat }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="id_driver{{ $item->id }}" class="form-label">Nama
                                                        Driver</label>
                                                    <input type="text" class="form-control"
                                                        id="id_driver{{ $item->id }}" name="id_driver"
                                                        value="{{ $item->id_driver }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="keterangan{{ $item->id }}"
                                                        class="form-label">Keterangan</label>
                                                    <input type="text" class="form-control"
                                                        id="keterangan{{ $item->id }}" name="keterangan"
                                                        value="{{ $item->keterangan }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="status{{ $item->id }}" class="form-label">Status</label>
                                                    <select class="form-select select2" id="status{{ $item->id }}"
                                                        name="status" required>
                                                        <option value="Masuk" {{ $item->status == 'Masuk' ? 'selected' :
                                                            '' }}>Masuk
                                                        </option>
                                                        <option value="Keluar" {{ $item->status == 'Keluar' ? 'selected'
                                                            : '' }}>Keluar
                                                        </option>
                                                        <option value="Cuti" {{ $item->status == 'Cuti' ? 'selected' :
                                                            '' }}>Cuti</option>
                                                    </select>
                                                </div>
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

<!-- Modal Tambah Mobil -->
<div class="modal fade" id="addPemesananMobilModal" tabindex="-1" aria-labelledby="addPemesananMobilModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPemesananMobilModalLabel">Tambah Data Pemesanan Mobil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="id_karyawan" class="form-label">Nama Karyawan</label>
                                <select class="form-select select2" id="id_karyawan" name="id_karyawan" required>
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($karyawan as $karyawans)
                                    <option value="{{ $karyawans->id }}">{{ $karyawans->nik }} | {{ $karyawans->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="tujuan" class="form-label">Tujuan</label>
                                <input type="text" class="form-control" id="tujuan" name="tujuan" required>
                            </div>
                            <div class="form-group mb-3"></div>
                            <label for="jam_berangkat" class="form-label">Jam Berangkat</label>
                            <input type="time" class="form-control" id="jam_berangkat" name="jam_berangkat" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="id_driver" class="form-label">Nama Driver</label>
                            <select class="form-select select2" id="id_driver" name="id_driver" required>
                                <option value="">Pilih Driver</option>
                                @foreach($driver as $drivers)
                                <option value="{{ $drivers->id }}">{{ $drivers->nama_driver }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select select2" id="status" name="status" required>
                                <option value="Masuk">Masuk</option>
                                <option value="Keluar">Keluar</option>
                                <option value="Cuti">Cuti</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
        </div>
        </form>
    </div>
</div>



@endsection

@push('page-script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@push('after-script')
<script>
    $(document).ready(function() {
        $('#pemesananMobilTable').DataTable({
            responsive: true
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
    });

    // Fungsi preview image saat upload
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result).show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Fungsi preview image saat edit
    function previewEditImage(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#preview_edit_' + id).attr('src', e.target.result).show();
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush