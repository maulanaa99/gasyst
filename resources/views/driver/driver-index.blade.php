@extends('layout.master')

@push('page-styles')
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
                <h5 class="card-title mb-0 text-md-start text-center">List Driver</h5>
            </div>
            <div class="d-md-flex justify-content-between align-items-center dt-layout-end col-md-auto mt-0">
                <div class="dt-buttons btn-group flex-wrap">
                    <div class="btn-group"><button
                            class="btn buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                            tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false"><span><span class="d-flex align-items-center gap-2"><i
                                        class="icon-base ri ri-external-link-line icon-18px"></i> <span
                                        class="d-none d-sm-inline-block">Export</span></span></span></button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="exportTable('print')">
                                        <i class="icon-base ri ri-printer-line me-2"></i>Print
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="exportTable('excel')">
                                        <i class="icon-base ri ri-file-excel-line me-2"></i>Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="exportTable('pdf')">
                                        <i class="icon-base ri ri-file-pdf-line me-2"></i>PDF
                                    </a>
                                </li>
                            </ul>
                    </div>
                    <button class="btn create-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                        type="button" data-bs-toggle="modal" data-bs-target="#addDriverModal"><span><span
                                class="d-flex align-items-center"><i
                                    class="icon-base ri ri-add-line icon-18px me-sm-1"></i><span
                                    class="d-none d-sm-inline-block">Add New Record</span></span></span></button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="driverTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Driver</th>
                        <th>Plat No</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($driver as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($item->driver_image==null)
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar me-2"><span
                                            class="avatar-initial rounded-circle bg-label-success">{{
                                            $item->nama_driver[0] }}</span></div>
                                </div>
                                <div class="d-flex flex-column"><span
                                        class="emp_name text-truncate text-heading fw-medium">{{ $item->nama_driver
                                        }}</span><small class="emp_post text-truncate">{{ $item->outsourching }}</small>
                                </div>
                            </div>
                            @else
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar me-2">
                                        <img src="{{ asset('storage/' . $item->driver_image) }}" alt="Driver Image"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="d-flex flex-column"><span
                                        class="emp_name text-truncate text-heading fw-medium">{{ $item->nama_driver
                                        }}</span><small class="emp_post text-truncate">{{ $item->outsourching }}</small>
                                </div>
                            </div>
                            @endif
                        </td>
                        <td>{{ $item->mobil ? $item->mobil->plat_no : '-' }}</td>
                        <td> {{ $item->karyawan->nama_karyawan }} </td>
                        @if ($item->status == 'Tersedia')
                        <td><span class="badge rounded-pill  bg-label-success">{{ $item->status }}</span>
                        </td>
                        @else
                        <td><span class="badge rounded-pill  bg-label-danger">{{ $item->status }}</span></td>
                        @endif
                        <td>
                            <button type="button" class="btn btn-icon btn-warning btn-edit waves-effect waves-light"
                                data-id="{{ $item->id }}" data-nama="{{ $item->nama_driver }}"
                                data-outsourching="{{ $item->outsourching }}" data-mobil="{{ $item->id_mobil }}"
                                data-karyawan="{{ $item->id_karyawan }}" data-status="{{ $item->status }}">
                                <i class="icon-base ri ri-edit-line icon-18px" style="color: white"></i>
                            </button>
                            <form action="{{ route('driver.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-icon btn-danger btn-fab demo waves-effect waves-light"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"> <i
                                        class="icon-base ri ri-delete-bin-line icon-18px" style="color: white"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Driver -->
<div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDriverModalLabel">Tambah Data Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('driver.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="text" class="form-control" id="nama_driver" name="nama_driver" placeholder="Masukkan nama driver" required>
                                <label for="nama_driver">Nama Driver</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="text" class="form-control" id="outsourching" name="outsourching" placeholder="Masukkan outsourching" required>
                                <label for="outsourching">Outsourching</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="id_mobil" name="id_mobil" required>
                                    <option value="" disabled selected>Pilih Mobil</option>
                                    @foreach($mobils as $mobil)
                                    <option value="{{ $mobil->id }}">{{ $mobil->nama_mobil }} - {{ $mobil->plat_no }}</option>
                                    @endforeach
                                </select>
                                <label for="id_mobil">Mobil</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="id_karyawan" name="id_karyawan" required>
                                    <option value="" disabled selected>Pilih User</option>
                                    @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                                <label for="id_karyawan">User</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="file" class="form-control" id="driver_image" name="driver_image" required>
                                <label for="driver_image">Image</label>
                                <div class="mt-2">
                                    <img id="preview" src="#" alt="Preview" style="max-height: 100px; display: none;">
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="status" name="status" required>
                                    <option value="" disabled selected>Pilih Status</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Dipesan">Dipesan</option>
                                    <option value="Dalam Perjalanan">Dalam Perjalanan</option>
                                    <option value="Servis">Servis</option>
                                </select>
                                <label for="status">Status</label>
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

<!-- Modal Edit Driver -->
<div class="modal fade" id="editDriverModal" tabindex="-1" aria-labelledby="editDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDriverModalLabel">Edit Data Driver</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('driver.update', $item->id) }}" method="POST" id="editDriverForm"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="driver_id" id="edit_driver_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="text" class="form-control" id="edit_nama_driver" name="nama_driver" placeholder="Masukkan nama driver" required>
                                <label for="edit_nama_driver">Nama Driver</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="text" class="form-control" id="edit_outsourching" name="outsourching" placeholder="Masukkan outsourching" required>
                                <label for="edit_outsourching">Outsourching</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="edit_id_mobil" name="id_mobil" required>
                                    <option value="" disabled selected>Pilih Mobil</option>
                                    @foreach($mobils as $mobil)
                                    <option value="{{ $mobil->id }}">{{ $mobil->nama_mobil }} - {{ $mobil->plat_no }}</option>
                                    @endforeach
                                </select>
                                <label for="edit_id_mobil">Mobil</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="edit_id_karyawan" name="id_karyawan" required>
                                    <option value="" disabled selected>Pilih User</option>
                                    @foreach($karyawans as $karyawan)
                                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama_karyawan }}</option>
                                    @endforeach
                                </select>
                                <label for="edit_id_karyawan">User</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="file" class="form-control" id="edit_driver_image" name="driver_image">
                                <label for="edit_driver_image">Image</label>
                                <div class="mt-2">
                                    <img id="current_image" src="" alt="Current Driver Image" style="max-height: 100px; display: none;">
                                    <img id="preview_edit" src="#" alt="Preview" style="max-height: 100px; display: none;">
                                </div>
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="edit_status" name="status" required>
                                    <option value="" disabled>Pilih Status</option>
                                    <option value="Tersedia">Tersedia</option>
                                    <option value="Dipesan">Dipesan</option>
                                    <option value="Dalam Perjalanan">Dalam Perjalanan</option>
                                    <option value="Servis">Servis</option>
                                </select>
                                <label for="edit_status">Status</label>
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


@endsection

@push('page-script')
@endpush

@push('after-script')
<script>
    $(document).ready(function() {
        $('#driverTable').DataTable({
            responsive: true,
            buttons: [
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'buttons-print d-none',
                    exportOptions: { columns: [0, 1, 2, 3, 4] }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'buttons-excel d-none',
                    exportOptions: { columns: [0, 1, 2, 3, 4] }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    className: 'buttons-pdf d-none',
                    exportOptions: { columns: [0, 1, 2, 3, 4] }
                }
            ]
        });

        // Fungsi untuk export tabel
        window.exportTable = function(type) {
            const table = $('#driverTable').DataTable();
            switch(type) {
                case 'print':
                    table.button('.buttons-print').trigger();
                    break;
                case 'excel':
                    table.button('.buttons-excel').trigger();
                    break;
                case 'pdf':
                    table.button('.buttons-pdf').trigger();
                    break;
            }
        };

        // Handle click pada tombol edit
        $(document).on('click', '.btn-edit', function() {
            // Ambil data dari atribut data-*
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var outsourching = $(this).data('outsourching');
            var mobil = $(this).data('mobil');
            var karyawan = $(this).data('karyawan');
            var status = $(this).data('status');
            var driver_image = $(this).closest('tr').find('img').attr('src');

            // Set nilai ke form edit
            $('#edit_driver_id').val(id);
            $('#edit_nama_driver').val(nama);
            $('#edit_outsourching').val(outsourching);

            // Set value untuk select2 dan refresh untuk update tampilan
            $('#edit_id_mobil').val(mobil).trigger('change');
            $('#edit_id_karyawan').val(karyawan).trigger('change');
            $('#edit_status').val(status).trigger('change');

            // Set current image
            if (driver_image) {
                $('#current_image').attr('src', driver_image).show();
            } else {
                $('#current_image').hide();
            }

            // Reset preview image
            $('#preview_edit').hide();

            // Set action URL form
            $('#editDriverForm').attr('action', '/driver/' + id);

            // Tampilkan modal
            $('#editDriverModal').modal('show');
        });

        // Handle image preview untuk form tambah
        $('#driver_image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });
    });

    // Fungsi preview image saat edit
    function previewEditImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_edit').attr('src', e.target.result).show();
                $('#current_image').hide();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
