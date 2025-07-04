@extends('layout.master')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@push('page-styles')
{{--
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
    rel="stylesheet" /> --}}
    <style>
        .form-check-input {
            cursor: pointer;
        }

        #bulkActionGroup {
            transition: all 0.3s ease;
        }

        .table th:first-child,
        .table td:first-child {
            width: 50px;
            text-align: center;
        }

        .ri-loader-4-line {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .icon-48px {
            font-size: 48px;
        }

        .table tbody tr td[colspan] {
            background-color: #f8f9fa;
        }

        /* Style for selected rows */
        #mobilTable tbody tr.selected,
        #mobilTable tbody tr.selected:hover {
            background-color: #e7e9ff !important; /* Light purple-blue for selected row */
        }

        /* Remove hover effect from all rows */
        #mobilTable tbody tr:hover {
            background-color: transparent !important;
        }
    </style>
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
                <div>
                    <h5 class="card-title mb-0 text-md-start text-center">List Mobil</h5>
                    <small class="text-muted">Total: {{ $mobil->count() }} data</small>
                    <small class="text-primary ms-2" id="selectedInfo" style="display: none;">
                        (<span id="selectedCountInfo">0</span> dipilih)
                    </small>
                </div>
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
                    <div class="btn-group me-2" id="bulkActionGroup" style="display: none;">
                        <button class="btn btn-warning btn-sm" onclick="bulkEdit()">
                            <i class="icon-base ri ri-edit-line me-1"></i>Edit Selected (<span id="selectedCount">0</span>)
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="bulkDelete()">
                            <i class="icon-base ri ri-delete-bin-line me-1"></i>Delete Selected (<span id="selectedCount2">0</span>)
                        </button>
                    </div>
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
            <table class="table table-bordered" id="mobilTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" class="form-check-input" title="Pilih semua">
                        </th>
                        <th>No</th>
                        <th>Image</th>
                        <th>Nama Mobil</th>
                        <th>Plat No</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if($mobil->count() > 0)
                    @foreach ($mobil as $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input row-checkbox" value="{{ $item->id }}" title="Pilih {{ $item->nama_mobil }}">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if($item->car_image)
                            <img src="{{ Storage::url($item->car_image) }}" alt="{{ $item->nama_mobil }}"
                                style="max-height: 50px;">
                            @else
                            <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>{{ $item->nama_mobil }}</td>
                        <td>{{ $item->plat_no }}</td>
                        <td>
                            @if ($item->status == 'tersedia')
                            <span class="badge rounded-pill  bg-label-success">{{ $item->status }}</span>
                            @else
                            <span class="badge rounded-pill  bg-label-danger">{{ $item->status }}</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-warning btn-sm waves-effect waves-light"
                                data-bs-toggle="modal" data-bs-target="#editMobilModal{{ $item->id }}">
                                <i class="icon-base ri ri-edit-line icon-18px" style="color: white"></i>
                            </button>
                            <form action="{{ route('mobil.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-danger btn-sm waves-effect waves-light"
                                    onclick="return confirmDelete('{{ $item->nama_mobil }}')">
                                    <i class="icon-base ri ri-delete-bin-line icon-18px" style="color: white"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal Edit untuk item ini -->
                    <div class="modal fade" id="editMobilModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="editMobilModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editMobilModalLabel{{ $item->id }}">Edit Data Mobil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('mobil.update', $item->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <input type="text" class="form-control"
                                                        id="nama_mobil{{ $item->id }}" name="nama_mobil"
                                                        value="{{ $item->nama_mobil }}"
                                                        placeholder="Masukkan nama mobil" required>
                                                    <label for="nama_mobil{{ $item->id }}">Nama Mobil</label>
                                                </div>
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <input type="text" class="form-control" id="plat_no{{ $item->id }}"
                                                        name="plat_no" value="{{ $item->plat_no }}"
                                                        placeholder="Masukkan plat nomor" required>
                                                    <label for="plat_no{{ $item->id }}">Plat No</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <input type="file" class="form-control"
                                                        id="car_image{{ $item->id }}" name="car_image"
                                                        onchange="previewEditImage(this, {{ $item->id }})">
                                                    <label for="car_image{{ $item->id }}">Image</label>
                                                    @if($item->car_image)
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($item->car_image) }}"
                                                            alt="{{ $item->nama_mobil }}" style="max-height: 100px;">
                                                    </div>
                                                    @endif
                                                    <div class="mt-2">
                                                        <img id="preview_edit_{{ $item->id }}" src="#" alt="Preview"
                                                            style="max-height: 100px; display: none;">
                                                    </div>
                                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah
                                                        gambar</small>
                                                </div>
                                                <div class="form-floating form-floating-outline mb-6">
                                                    <select class="form-select select2" id="status{{ $item->id }}"
                                                        name="status" required>
                                                        <option value="" disabled>Pilih Status</option>
                                                        <option value="tersedia" {{ $item->status == 'tersedia' ?
                                                            'selected' :
                                                            '' }}>Tersedia</option>
                                                        <option value="dipesan" {{ $item->status == 'dipesan' ?
                                                            'selected' : '' }}>Dipesan</option>
                                                        <option value="dalam_perjalanan" {{ $item->status ==
                                                            'dalam_perjalanan' ?
                                                            'selected' : '' }}>Dalam Perjalanan</option>
                                                        <option value="servis" {{ $item->status == 'servis' ?
                                                            'selected' : '' }}>Servis</option>
                                                    </select>
                                                    <label for="status{{ $item->id }}">Status</label>
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
                    @else
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="text-muted">
                                <i class="icon-base ri ri-car-line icon-48px mb-2"></i>
                                <p class="mb-0">Tidak ada data mobil</p>
                                <small>Klik tombol "Add New Record" untuk menambahkan data mobil</small>
                            </div>
                        </td>
                    </tr>
                    @endif
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="text" class="form-control" id="nama_mobil" name="nama_mobil"
                                    placeholder="Masukkan nama mobil" required>
                                <label for="nama_mobil">Nama Mobil</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="text" class="form-control" id="plat_no" name="plat_no"
                                    placeholder="Masukkan plat nomor" required>
                                <label for="plat_no">Plat No</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating form-floating-outline mb-6">
                                <input type="file" class="form-control" id="car_image" name="car_image" required
                                    onchange="previewImage(this)">
                                <label for="car_image">Image</label>
                                <div class="mt-2">
                                    <img id="preview" src="#" alt="Preview" style="max-height: 100px; display: none;">
                                </div>
                            </div>
                            <div class="form-floating form-floating-outline mb-6">
                                <select class="form-select select2" id="status" name="status" required>
                                    <option value="" disabled selected>Pilih Status</option>
                                    <option value="tersedia">Tersedia</option>
                                    <option value="dipesan">Dipesan</option>
                                    <option value="dalam_perjalanan">Dalam Perjalanan</option>
                                    <option value="servis">Servis</option>
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

@endsection

@push('page-script')
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
@endpush

@push('after-script')
<script>
    $(document).ready(function() {
        // Fallback untuk SweetAlert jika tidak tersedia
        if (typeof Swal === 'undefined') {
            window.Swal = {
                fire: function(options) {
                    const result = confirm(options.text || 'Konfirmasi?');
                    return Promise.resolve({ isConfirmed: result });
                }
            };
        }

        $('#mobilTable').DataTable({
            responsive: true,
            columnDefs: [
                {
                    targets: 0, // kolom checkbox
                    orderable: false,
                    searchable: false,
                    width: '50px'
                }
            ],
            order: [[1, 'asc']], // urutkan berdasarkan kolom No (indeks 1)

            buttons: [
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'buttons-print d-none',
                    exportOptions: { columns: [1, 2, 3, 4, 5] } // exclude kolom checkbox
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'buttons-excel d-none',
                    exportOptions: { columns: [1, 2, 3, 4, 5] } // exclude kolom checkbox
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    className: 'buttons-pdf d-none',
                    exportOptions: { columns: [1, 2, 3, 4, 5] } // exclude kolom checkbox
                }
            ]
        });

        // Fungsi untuk export tabel
        window.exportTable = function(type) {
            const table = $('#mobilTable').DataTable();
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

        // Fungsi untuk checkbox select all
        $('#selectAll').change(function() {
            const isChecked = $(this).prop('checked');
            $('.row-checkbox').prop('checked', isChecked);
            $('.row-checkbox').closest('tr').toggleClass('selected', isChecked);
            updateBulkActionVisibility();
        });

        // Fungsi untuk checkbox individual
        $(document).on('change', '.row-checkbox', function() {
            $(this).closest('tr').toggleClass('selected', this.checked);
            updateBulkActionVisibility();
            updateSelectAllCheckbox();
        });

        // Fungsi untuk update visibility bulk action
        function updateBulkActionVisibility() {
            const checkedCount = $('.row-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#bulkActionGroup').show();
                $('#selectedCount').text(checkedCount);
                $('#selectedCount2').text(checkedCount);
                $('#selectedInfo').show();
                $('#selectedCountInfo').text(checkedCount);
            } else {
                $('#bulkActionGroup').hide();
                $('#selectedCount').text('0');
                $('#selectedCount2').text('0');
                $('#selectedInfo').hide();
                $('#selectedCountInfo').text('0');
            }
        }

        // Fungsi untuk update select all checkbox
        function updateSelectAllCheckbox() {
            const totalCheckboxes = $('.row-checkbox').length;
            const checkedCheckboxes = $('.row-checkbox:checked').length;

            if (checkedCheckboxes === 0) {
                $('#selectAll').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAll').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAll').prop('indeterminate', true);
            }
        }
    });

    // Fungsi bulk edit
    function bulkEdit() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih setidaknya satu item untuk diedit dengan mencentang checkbox di sebelah kiri tabel',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (selectedIds.length > 1) {
            // Dapatkan nama mobil yang dipilih
            const selectedMobilNames = [];
            selectedIds.forEach(id => {
                const row = $(`.row-checkbox[value="${id}"]`).closest('tr');
                const namaMobil = row.find('td:eq(3)').text().trim(); // kolom nama mobil
                selectedMobilNames.push(namaMobil);
            });

            const detailMessage = `Mobil yang dipilih:\n${selectedMobilNames.slice(0, 3).join(', ')}${selectedMobilNames.length > 3 ? ' dan ' + (selectedMobilNames.length - 3) + ' lainnya' : ''}`;

            Swal.fire({
                icon: 'info',
                title: 'Informasi',
                html: `<p>Fitur edit multiple belum tersedia. Silakan pilih satu item saja.</p><small class="text-muted">${detailMessage}</small>`,
                confirmButtonText: 'OK'
            });
            return;
        }

        // Buka modal edit untuk item pertama yang dipilih
        $('#editMobilModal' + selectedIds[0]).modal('show');
    }

    // Fungsi bulk delete
    function bulkDelete() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Pilih setidaknya satu item untuk dihapus dengan mencentang checkbox di sebelah kiri tabel',
                confirmButtonText: 'OK'
            });
            return;
        }

        const confirmMessage = selectedIds.length === 1
            ? 'Apakah Anda yakin ingin menghapus item yang dipilih?'
            : 'Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' item yang dipilih?';

        // Dapatkan nama mobil yang akan dihapus
        const selectedMobilNames = [];
        selectedIds.forEach(id => {
            const row = $(`.row-checkbox[value="${id}"]`).closest('tr');
            const namaMobil = row.find('td:eq(3)').text().trim(); // kolom nama mobil
            selectedMobilNames.push(namaMobil);
        });

        const detailMessage = selectedIds.length === 1
            ? `Mobil: ${selectedMobilNames[0]}`
            : `Mobil yang akan dihapus:\n${selectedMobilNames.slice(0, 3).join(', ')}${selectedMobilNames.length > 3 ? ' dan ' + (selectedMobilNames.length - 3) + ' lainnya' : ''}`;

        Swal.fire({
            icon: 'warning',
            title: 'Konfirmasi Hapus',
            html: `<p>${confirmMessage}</p><small class="text-muted">${detailMessage}</small>`,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Tampilkan loading overlay
                Swal.fire({
                    title: 'Menghapus data...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Buat form untuk bulk delete
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("mobil.bulk-delete") }}';

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

                // Tambahkan IDs yang dipilih
                selectedIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Fungsi untuk mendapatkan ID yang dipilih
    function getSelectedIds() {
        const selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        return selectedIds;
    }

    // Fungsi untuk konfirmasi delete individual
    function confirmDelete(namaMobil) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: 'warning',
                title: 'Konfirmasi Hapus',
                html: `<p>Apakah Anda yakin ingin menghapus mobil ini?</p><small class="text-muted">Mobil: ${namaMobil}</small>`,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({
                        title: 'Menghapus data...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
                return result.isConfirmed;
            });
        } else {
            return confirm(`Apakah Anda yakin ingin menghapus mobil "${namaMobil}"?`);
        }
    }

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

    // Tampilkan notifikasi sukses jika ada
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Tampilkan notifikasi error jika ada
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session("error") }}',
            confirmButtonText: 'OK'
        });
    @endif
</script>
@endpush