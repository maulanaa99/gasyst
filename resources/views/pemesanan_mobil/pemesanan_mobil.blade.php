@extends('layout.master')

@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
@endphp

@push('page-styles')

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

                @if(Auth::user()->role !== 'security')
                <div class="dt-buttons flex-wrap">
                    <div class="btn-group">
                        <button
                            class="btn btn-sm buttons-collection btn-label-primary dropdown-toggle me-4 waves-effect border-none"
                            tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span>
                                <span class="d-flex align-items-center gap-2">
                                    <i class="icon-base ri ri-external-link-line icon-18px"></i>
                                    <span class="d-none d-sm-inline-block">Export</span>
                                </span>
                            </span>
                        </button>
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
                    <button class="btn btn-sm btn-danger me-2" id="deleteSelectedBtn" style="display: none;">
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-delete-bin-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Hapus Terpilih</span>
                        </span>
                        <button type="button" class="btn btn-primary waves-effect waves-light"
                            onclick="window.location.href='{{ route('surat-jalan.create') }}'">
                            <span class="icon-base ri ri-add-line icon-18px me-2"></span>Tambah Pemesanan Mobil
                        </button>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    {{-- <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary" id="filter_date">Filter</button>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="pemesananMobilTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Tujuan</th>
                        <th>Jam Berangkat</th>
                        <th>Jam Kembali</th>
                        <th>Nama Driver</th>
                        <th>PIC</th>
                        <th>Keterangan</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suratJalan as $item)
                    <tr data-id="{{ $item->id }}">
                        <td><input type="checkbox" class="form-check-input item-checkbox" data-id="{{ $item->id }}">
                        </td>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ date('d/m/y', strtotime($item->tanggal)) }}</td>
                        <td>
                            @if($item->jenis_pemesanan === 'Driver Only')
                                <span class="badge rounded-pill  bg-label-info">Driver Only</span>
                            @elseif($item->karyawans->count() > 0)
                                {{ $item->karyawans->pluck('nama_karyawan')->join(', ') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->lokasis->count() > 0)
                                {{ $item->lokasis->unique('id')->pluck('kode_lokasi')->join(', ') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($item->jam_berangkat_aktual)
                            <span class="text-success">Aktual : {{ $item->jam_berangkat_aktual }}</span>
                            @else
                            <span class="text-danger">Est : {{ $item->jam_berangkat }}</span>
                            @if((Auth::user()->role === 'superadmin' || Auth::user()->role === 'security') &&
                            !$item->status_jam_berangkat_aktual)
                            <div class="d-flex align-items-center mt-1">
                                <button type="button" class="btn btn-sm btn-info ms-2 btn-set-jam-berangkat"
                                    data-id="{{ $item->id }}">
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
                                <button type="button" class="btn btn-sm btn-info ms-2 btn-set-jam-kembali"
                                    data-id="{{ $item->id }}">
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
                            @if(Auth::user()->role === 'security' && !$item->status_approve)
                            <button type="button"
                                class="btn btn-icon btn-success btn-sm waves-effect waves-light btn-approve"
                                data-id="{{ $item->id }}">
                                <i class="icon-base ri ri-check-line icon-18px" style="color: white"></i>
                            </button>
                            @endif
                            <a href="{{ route('surat-jalan.print', $item->id) }}" target="_blank"
                                class="btn btn-icon btn-info btn-sm waves-effect waves-light">
                                <i class="icon-base ri ri-printer-line icon-18px" style="color: white"></i>
                            </a>
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
                                                                value="Karyawan" {{ $item->jenis_pemesanan === 'Karyawan' ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="jenisKaryawan{{ $item->id }}">
                                                                Karyawan
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio"
                                                                name="jenis_pemesanan"
                                                                id="jenisDriverOnly{{ $item->id }}" value="Driver Only"
                                                                {{ $item->jenis_pemesanan === 'Driver Only' ? 'checked' : '' }}>
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
                                            <div class="form-group mb-3">
                                                <label for="tanggal{{ $item->id }}" class="form-label">Tanggal</label>
                                                <input type="date" class="form-control" id="tanggal{{ $item->id }}"
                                                    name="tanggal" value="{{ $item->tanggal }}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="no_surat_jalan{{ $item->id }}" class="form-label">Nomor
                                                    Surat Jalan</label>
                                                <input type="text" class="form-control"
                                                    id="no_surat_jalan{{ $item->id }}" name="no_surat_jalan"
                                                    value="{{ $item->no_surat_jalan }}" readonly>
                                            </div>
                                            <div id="formKaryawan{{ $item->id }}"
                                                style="display: {{ $item->jenis_pemesanan === 'Karyawan' ? 'block' : 'none' }}">
                                                <div class="form-group mb-3">
                                                    <label for="id_karyawan{{ $item->id }}" class="form-label">Nama
                                                        Karyawan</label>
                                                    <div class="input-group">
                                                        <select class="form-select select2"
                                                            id="id_karyawan{{ $item->id }}" name="karyawan_id[]"
                                                            multiple {{ $item->jenis_pemesanan === 'Karyawan' ? 'required' : '' }}>
                                                            @foreach($karyawan as $k)
                                                            <option value="{{ $k->id }}" {{ in_array($k->id,
                                                                $item->karyawans->pluck('id')->toArray()) ? 'selected' :
                                                                '' }}>
                                                                {{ $k->nik }} | {{ $k->nama_karyawan }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="formDriverOnly{{ $item->id }}"
                                                style="display: {{ $item->jenis_pemesanan === 'Driver Only' ? 'block' : 'none' }}">
                                                <div class="form-group mb-3">
                                                    <label for="id_departemen{{ $item->id }}"
                                                        class="form-label">Departemen</label>
                                                    <select class="form-select select2"
                                                        id="id_departemen{{ $item->id }}" name="id_departemen"
                                                        {{ $item->jenis_pemesanan === 'Driver Only' ? 'required' : '' }}>
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
                                                <label for="jam_berangkat{{ $item->id }}" class="form-label">Jam
                                                    Berangkat</label>
                                                <input type="time" class="form-control"
                                                    id="jam_berangkat{{ $item->id }}" name="jam_berangkat"
                                                    value="{{ $item->jam_berangkat }}" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="jam_kembali{{ $item->id }}" class="form-label">Jam
                                                    Kembali</label>
                                                <input type="time" class="form-control" id="jam_kembali{{ $item->id }}"
                                                    name="jam_kembali" value="{{ $item->jam_kembali }}" required>
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

                                            <div class="form-group mb-3">
                                                <label for="keterangan{{ $item->id }}"
                                                    class="form-label">Keterangan</label>
                                                <textarea class="form-control h-px-100" id="keterangan{{ $item->id }}"
                                                    name="keterangan" required>{{ $item->keterangan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="id_lokasi{{ $item->id }}" class="form-label">Lokasi</label>
                                            <select class="form-select select2" id="id_lokasi{{ $item->id }}"
                                                name="lokasi_id[]" multiple required>
                                                @foreach($lokasi as $l)
                                                <option value="{{ $l->id }}" {{ in_array($l->id,
                                                    $item->lokasis->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $l->nama_lokasi }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" class="form-control" id="PIC{{ $item->id }}" name="PIC"
                                            value="{{ auth()->user()->name }}" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                                aria-hidden="true" id="submitSpinner{{ $item->id }}"></span>
                                            <span id="submitText{{ $item->id }}">Simpan Perubahan</span>
                                        </button>
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





@endsection

@push('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('after-script')
<script src="{{ asset('assets/vendor/libs/datatables-buttons/datatables-buttons.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/jszip/jszip.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/pdfmake/pdfmake.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-buttons/buttons.html5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-buttons/buttons.print.js') }}"></script>
<script>
    // Fungsi untuk mendapatkan CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    }

    $(document).ready(function() {
        // Set nilai default tanggal ke hari ini
        const today = new Date().toISOString().split('T')[0];
        $('#start_date').val(today);
        $('#end_date').val(today);

        // Inisialisasi DataTable
        const table = $('#pemesananMobilTable').DataTable({
            order: [[1, 'asc']],
            buttons: [
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'buttons-print d-none',
                    exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] }
                },
                {
                    extend: 'excel',
                    text: 'Excel',
                    className: 'buttons-excel d-none',
                    exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] }
                },
                {
                    extend: 'pdf',
                    text: 'PDF',
                    className: 'buttons-pdf d-none',
                    exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] }
                }
            ],
            columnDefs: [
                {
                    targets: 0,
                    orderable: false,
                    searchable: false
                },
                {
                    targets: -1,
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Fungsi untuk filter tanggal
        function filterByDate() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (startDate && endDate) {
                // Konversi format tanggal untuk perbandingan
                const start = new Date(startDate + 'T00:00:00');
                const end = new Date(endDate + 'T23:59:59');

                // Filter data berdasarkan tanggal
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    const dateStr = data[2]; // Kolom tanggal (index 2)
                    if (!dateStr) return false;

                    try {
                        // Parse tanggal dari format dd/mm/yy
                        const [day, month, year] = dateStr.split('/');
                        const rowDate = new Date(2000 + parseInt(year), parseInt(month) - 1, parseInt(day));

                        // Debug log
                        console.log('Row Date:', rowDate);
                        console.log('Start Date:', start);
                        console.log('End Date:', end);

                        // Periksa apakah tanggal berada dalam rentang
                        const isInRange = rowDate >= start && rowDate <= end;
                        console.log('Is in range:', isInRange);

                        return isInRange;
                    } catch (error) {
                        console.error('Error parsing date:', error);
                        return false;
                    }
                });

                table.draw();
                // Hapus filter setelah digunakan
                $.fn.dataTable.ext.search.pop();
            } else {
                // Jika tanggal tidak lengkap, tampilkan semua data
                table.draw();
            }
        }

        // Event listener untuk tombol filter
        $('#filter_date').on('click', function() {
            filterByDate();
        });

        // Event listener untuk input tanggal
        $('#start_date, #end_date').on('change', function() {
            if ($('#start_date').val() && $('#end_date').val()) {
                filterByDate();
            }
        });

        // Jalankan filter saat halaman dimuat
        $(window).on('load', function() {
            setTimeout(function() {
                filterByDate();
            }, 500);
        });

        // Fungsi untuk export tabel
        window.exportTable = function(type) {
            const exportOptions = {
                columns: [1, 2, 3, 4, 5, 6, 7, 8, 9]
            };

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

        // Handle Select All checkbox
        $('#selectAll').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.item-checkbox').prop('checked', isChecked);
            updateDeleteButtonVisibility();
        });

        // Handle individual checkboxes
        $(document).on('change', '.item-checkbox', function() {
            updateSelectAllState();
            updateDeleteButtonVisibility();
        });

        // Function to update select all checkbox state
        function updateSelectAllState() {
            const totalCheckboxes = $('.item-checkbox').length;
            const checkedCheckboxes = $('.item-checkbox:checked').length;
            $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
        }

        // Function to update delete button visibility
        function updateDeleteButtonVisibility() {
            const checkedCount = $('.item-checkbox:checked').length;
            $('#deleteSelectedBtn').toggle(checkedCount > 0);
        }

        // Handle delete selected button click
        $('#deleteSelectedBtn').click(function() {
            const selectedIds = $('.item-checkbox:checked').map(function() {
                return $(this).data('id');
            }).get();

            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus ${selectedIds.length} data yang dipilih!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
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

                    // Kirim request untuk menghapus data yang dipilih
                    fetch('/surat-jalan/delete-selected', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({
                            ids: selectedIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Data yang dipilih berhasil dihapus',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat menghapus data');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat menghapus data'
                        });
                    });
                }
            });
        });

        // Event listener untuk tombol set jam kembali
        $(document).on('click', '.btn-set-jam-kembali', function() {
            const id = $(this).data('id');
            setJamKembali(id);
        });

        // Fungsi untuk mengisi jam kembali
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
                            jam_kembali: currentTime,
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

        // Event listener untuk tombol set jam berangkat
        $(document).on('click', '.btn-set-jam-berangkat', function() {
            const id = $(this).data('id');
            setJamBerangkat(id);
        });

        // Fungsi untuk mengisi jam berangkat
        function setJamBerangkat(id) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const currentTime = `${hours}:${minutes}`;

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
                            jam_berangkat: currentTime,
                            jam_berangkat_aktual: currentTime
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
        }

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

        // SweetAlert2 untuk konfirmasi hapus single item
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
        });

        // Handle approve button click
        $(document).on('click', '.btn-approve', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyetujui surat jalan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, setujui',
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

                    fetch(`/surat-jalan/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Terjadi kesalahan saat menyetujui surat jalan');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if(data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Surat jalan berhasil disetujui',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan saat menyetujui surat jalan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat menyetujui surat jalan'
                        });
                    });
                }
            });
        });
    });
</script>
@endpush
