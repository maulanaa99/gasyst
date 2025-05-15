@extends('layout.master')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('page-styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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
                <h5 class="card-title mb-0 text-md-start text-center">List Mobil</h5>
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
                        type="button" data-bs-toggle="modal" data-bs-target="#addMobilModal"><span><span
                                class="d-flex align-items-center"><i
                                    class="icon-base ri ri-add-line icon-18px me-sm-1"></i><span
                                    class="d-none d-sm-inline-block">Add New Record</span></span></span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="mobilTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Image</th>
                        <th>Nama Mobil</th>
                        <th>Plat No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mobil as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($item->car_image)
                                <img src="{{ Storage::url($item->car_image) }}" alt="{{ $item->nama_mobil }}" style="max-height: 50px;">
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $item->nama_mobil }}</td>
                        <td>{{ $item->plat_no }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            <button type="button" class="btn btn-icon btn-warning btn-sm waves-effect waves-light" data-bs-toggle="modal"
                                data-bs-target="#editMobilModal{{ $item->id }}">
                                <i class="icon-base ri ri-edit-line icon-18px" style="color: white"></i>
                            </button>
                            <form action="{{ route('mobil.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-danger btn-sm waves-effect waves-light"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    <i class="icon-base ri ri-delete-bin-line icon-18px" style="color: white"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit untuk item ini -->
                    <div class="modal fade" id="editMobilModal{{ $item->id }}" tabindex="-1" aria-labelledby="editMobilModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editMobilModalLabel{{ $item->id }}">Edit Data Mobil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('mobil.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="nama_mobil{{ $item->id }}" class="form-label">Nama Mobil</label>
                                                    <input type="text" class="form-control" id="nama_mobil{{ $item->id }}" name="nama_mobil" value="{{ $item->nama_mobil }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="plat_no{{ $item->id }}" class="form-label">Plat No</label>
                                                    <input type="text" class="form-control" id="plat_no{{ $item->id }}" name="plat_no" value="{{ $item->plat_no }}" required>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="car_image{{ $item->id }}" class="form-label">Image</label>
                                                    @if($item->car_image)
                                                        <div class="mb-2">
                                                            <img src="{{ Storage::url($item->car_image) }}" alt="{{ $item->nama_mobil }}" style="max-height: 100px;">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control" id="car_image{{ $item->id }}" name="car_image" onchange="previewEditImage(this, {{ $item->id }})">
                                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                                                    <div class="mt-2">
                                                        <img id="preview_edit_{{ $item->id }}" src="#" alt="Preview" style="max-height: 100px; display: none;">
                                                    </div>
                                                </div>
                                                <div class="form-group mb-3">
                                                    <label for="status{{ $item->id }}" class="form-label">Status</label>
                                                    <select class="form-select select2" id="status{{ $item->id }}" name="status" required>
                                                        <option value="Aktif" {{ $item->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                        <option value="Tidak Aktif" {{ $item->status == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
<div class="modal fade" id="addMobilModal" tabindex="-1" aria-labelledby="addMobilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMobilModalLabel">Tambah Data Mobil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('mobil.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="nama_mobil" class="form-label">Nama Mobil</label>
                                <input type="text" class="form-control" id="nama_mobil" name="nama_mobil" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="plat_no" class="form-label">Plat No</label>
                                <input type="text" class="form-control" id="plat_no" name="plat_no" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="car_image" class="form-label">Image</label>
                                <input type="file" class="form-control" id="car_image" name="car_image" required onchange="previewImage(this)">
                                <div class="mt-2">
                                    <img id="preview" src="#" alt="Preview" style="max-height: 100px; display: none;">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select select2" id="status" name="status" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('page-script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush

@push('after-script')
<script>
    $(document).ready(function() {
        $('#mobilTable').DataTable({
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
