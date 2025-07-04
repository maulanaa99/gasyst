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
            @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
            @endif
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                <h5 class="card-title mb-0 text-md-start text-center">List Pemesanan Mobil</h5>
            </div>

        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('surat-jalan.index') }}">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="start_date" name="start_date"
                                            value="{{ request('start_date', date('Y-m-d')) }}">
                                        <label for="start_date">Tanggal Awal</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="end_date" name="end_date"
                                            value="{{ request('end_date', date('Y-m-d')) }}">
                                        <label for="end_date">Tanggal Akhir</label>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary form-control">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(Auth::user()->role !== 'security')
                    <div class="d-flex gap-2">
                        <div class="btn-group">
                            <button
                                class="btn btn-sm buttons-collection btn-label-primary dropdown-toggle waves-effect border-none form-control"
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
                        <button type="button" class="btn btn-primary waves-effect waves-light form-control"
                            onclick="window.location.href='{{ route('surat-jalan.create') }}'">
                            <span class="icon-base ri ri-add-line icon-18px me-2"></span>Tambah
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="pemesananMobilTable">
                <thead>
                    <tr>
                        <th>@if(Auth::user()->role !== 'security')<input type="checkbox" class="form-check-input"
                                id="selectAll">@endif</th>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Karyawan</th>
                        <th>Tujuan</th>
                        <th>Jam Berangkat</th>
                        <th>Jam Kembali</th>
                        <th>Nama Driver</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suratJalan as $item)
                    <tr data-id="{{ $item->id }}">
                        <td>@if(Auth::user()->role !== 'security')<input type="checkbox"
                                class="form-check-input item-checkbox" data-id="{{ $item->id }}">@endif</td>
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
                            @if($item->jam_berangkat)
                            <span class="text-success">{{ $item->jam_berangkat }}</span>
                            @else
                            <span class="text-danger">Belum diisi</span>
                            @endif
                            @if((Auth::user()->role === 'hrga' || Auth::user()->role === 'security') && $item->status
                            === 'Dipesan' && !is_null($item->id_driver))
                            <div class="d-flex align-items-center mt-1">
                                <button type="button" class="btn btn-sm btn-info ms-2 btn-set-jam-berangkat"
                                    data-id="{{ $item->id }}">
                                    <i class="icon-base ri ri-time-line"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($item->jam_kembali)
                            <span class="text-success">{{ $item->jam_kembali }}</span>
                            @else
                            <span class="text-danger">Belum diisi</span>
                            @endif
                            @if((Auth::user()->role === 'hrga' || Auth::user()->role === 'security') && $item->status
                            === 'Dalam Perjalanan')
                            <div class="d-flex align-items-center mt-1">
                                <button type="button" class="btn btn-sm btn-info ms-2 btn-set-jam-kembali"
                                    data-id="{{ $item->id }}">
                                    <i class="icon-base ri ri-time-line"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($item->id_driver==null)
                            <div class="d-flex justify-content-start align-items-center user-name">
                                {{-- <div class="avatar-wrapper">
                                    <div class="avatar me-2"><span
                                            class="avatar-initial rounded-circle bg-label-success">{{
                                            $item->driver->nama_driver[0] ?? '-' }}</span></div>
                                </div> --}}
                                <div class="d-flex flex-column"><span
                                        class="emp_name text-truncate text-heading fw-medium">{{ $item->driver->nama_driver ?? '-'}}</span><small class="emp_post text-truncate">{{ $item->driver->outsourching ?? '-' }}</small>
                                </div>
                            </div>
                            @else
                            <div class="d-flex justify-content-start align-items-center user-name">
                                <div class="avatar-wrapper">
                                    <div class="avatar me-2">
                                        <img src="{{ asset('storage/' . $item->driver->driver_image) }}" alt="Driver Image"
                                            class="rounded-circle"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    </div>
                                </div>
                                <div class="d-flex flex-column"><span
                                        class="emp_name text-truncate text-heading fw-medium">{{ $item->driver->nama_driver ?? '-'
                                        }}</span><small class="emp_post text-truncate">{{ $item->driver->mobil->nama_mobil ?? '-' }}</small>
                                </div>
                            </div>
                            @endif
                        </td>

                        <td>{{ $item->keterangan }}</td>
                        <td>
                            @if($item->status === 'Dipesan')
                            <span class="badge rounded-pill bg-label-secondary">Dipesan</span>
                            @elseif($item->status === 'Dalam Perjalanan')
                            <span class="badge rounded-pill bg-label-primary">Dalam Perjalanan</span>
                            @elseif($item->status === 'Selesai')
                            <span class="badge rounded-pill bg-label-success">Selesai</span>
                            @elseif($item->status === 'Dibatalkan')
                            <span class="badge rounded-pill bg-label-danger">Dibatalkan</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                @if(Auth::user()->role === 'manager' &&
                                ($item->jenis_pemesanan === 'Driver Only' ?
                                ($item->departemen &&
                                $item->departemen->manager_id === Auth::id() &&
                                !$item->approve_by && !$item->approve_at) :
                                $item->suratJalanDetail->some(function($detail) {
                                return $detail->karyawan &&
                                $detail->karyawan->departemen &&
                                $detail->karyawan->departemen->manager_id === Auth::id();
                                }) && !$item->approve_by && !$item->approve_at))
                                <button type="button"
                                    class="btn btn-icon btn-success waves-effect waves-light btn-approve-manager"
                                    data-id="{{ $item->id }}">
                                    <i class="icon-base ri ri-check-double-line icon-18px" style="color: white"></i>
                                </button>
                                @endif
                                @if(Auth::user()->role === 'hrga' && !$item->acknowledged_by && !$item->acknowledged_at)
                                <button type="button"
                                    class="btn btn-icon btn-success waves-effect waves-light btn-approve-hrga"
                                    data-id="{{ $item->id }}">
                                    <i class="icon-base ri ri-check-double-line icon-18px" style="color: white"></i>
                                </button>
                                @endif
                                @if(Auth::user()->role === 'security' && !$item->checked_by && !$item->checked_at &&
                                $item->status === 'Selesai')
                                <button type="button"
                                    class="btn btn-icon btn-success waves-effect waves-light btn-check-security"
                                    data-id="{{ $item->id }}">
                                    <i class="icon-base ri ri-check-line icon-18px" style="color: white"></i>
                                </button>
                                @endif

                                <a href="{{ route('surat-jalan.print', $item->id) }}" target="_blank"
                                    class="btn btn-icon btn-info waves-effect waves-light">
                                    <i class="icon-base ri ri-printer-line icon-18px" style="color: white"></i>
                                </a>
                                @if(Auth::user()->role !== 'security')
                                @php
                                    $hideEdit = (in_array(Auth::user()->role, ['admin', 'security','manager']) && in_array($item->status, ['Dalam Perjalanan', 'Selesai']));
                                @endphp
                                @if(!$hideEdit)
                                <button type="button" class="btn btn-icon btn-warning waves-effect waves-light btn-edit"
                                    data-bs-toggle="modal" data-bs-target="#editPemesananMobilModal{{ $item->id }}">
                                    <i class="icon-base ri ri-edit-line icon-18px" style="color: white"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-icon btn-danger waves-effect waves-light delete-btn"
                                    data-id="{{ $item->id }}">
                                    <i class="icon-base ri ri-delete-bin-line icon-18px" style="color: white"></i>
                                </button>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editPemesananMobilModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="editPemesananMobilModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editPemesananMobilModalLabel{{ $item->id }}">Edit
                                        Pemesanan Mobil</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('surat-jalan.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <!-- Jenis Pemesanan -->
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Jenis Pemesanan</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input jenis-pemesanan-radio" type="radio"
                                                        name="jenis_pemesanan" id="jenisKaryawan{{ $item->id }}"
                                                        value="Karyawan" @if($item->jenis_pemesanan == 'Karyawan')
                                                    checked @endif>
                                                    <label class="form-check-label"
                                                        for="jenisKaryawan{{ $item->id }}">Karyawan</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input jenis-pemesanan-radio" type="radio"
                                                        name="jenis_pemesanan" id="jenisDriverOnly{{ $item->id }}"
                                                        value="Driver Only" @if($item->jenis_pemesanan == 'Driver Only')
                                                    checked @endif>
                                                    <label class="form-check-label"
                                                        for="jenisDriverOnly{{ $item->id }}">Driver Only</label>
                                                </div>
                                            </div>
                                            <!-- Tanggal -->
                                            <div class="col-md-6 mb-3">
                                                <label for="tanggal{{ $item->id }}" class="form-label">Tanggal</label>
                                                <input type="date" class="form-control" id="tanggal{{ $item->id }}"
                                                    name="tanggal"
                                                    value="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d') }}">
                                            </div>
                                            <!-- Nama Karyawan (hanya jika jenis_pemesanan = karyawan) -->
                                            <div class="col-md-6 mb-3 form-karyawan" @if($item->jenis_pemesanan !=
                                                'Karyawan') style="display:none;" @endif>
                                                <label for="karyawan_id{{ $item->id }}" class="form-label">Nama
                                                    Karyawan</label>
                                                <select class="form-select select2" id="karyawan_id{{ $item->id }}"
                                                    name="karyawan_id[]" multiple>
                                                    @foreach($karyawan as $k)
                                                    <option value="{{ $k->id }}" @if($item->
                                                        karyawans->pluck('id')->contains($k->id)) selected @endif>{{
                                                        $k->nama_karyawan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Departemen (hanya jika jenis_pemesanan = Driver Only) -->
                                            @php
                                            $selectedDepartemenId = $item->suratJalanDetail->first()->id_departemen ??
                                            null;
                                            @endphp
                                            <div class="col-md-6 mb-3 form-driveronly" @if($item->jenis_pemesanan !=
                                                'Driver Only') style="display:none;" @endif>
                                                <label for="departemen{{ $item->id }}"
                                                    class="form-label">Departemen</label>
                                                <select class="form-select select2" id="departemen{{ $item->id }}"
                                                    name="id_departemen">
                                                    <option value="">Pilih Departemen</option>
                                                    @foreach($departemen as $dpt)
                                                    <option value="{{ $dpt->id }}" @if($selectedDepartemenId==$dpt->id)
                                                        selected @endif>{{ $dpt->nama_departemen }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Tujuan -->
                                            <div class="col-md-6 mb-3">
                                                <label for="lokasi_id{{ $item->id }}" class="form-label">Tujuan</label>
                                                <select class="form-select select2" id="lokasi_id{{ $item->id }}"
                                                    name="lokasi_id[]" multiple>
                                                    @foreach($lokasi as $l)
                                                    <option value="{{ $l->id }}" @if($item->
                                                        lokasis->pluck('id')->contains($l->id)) selected @endif>{{
                                                        $l->kode_lokasi }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <!-- Jam Berangkat -->
                                            <div class="col-md-6 mb-3">
                                                <label for="jam_berangkat{{ $item->id }}" class="form-label">Jam
                                                    Berangkat</label>
                                                <input type="time" class="form-control"
                                                    id="jam_berangkat{{ $item->id }}" name="jam_berangkat"
                                                    value="{{ $item->jam_berangkat }}" @if($item->status !== 'Dipesan')
                                                disabled @endif>
                                            </div>
                                            <!-- Jam Kembali (readonly) -->
                                            <div class="col-md-6 mb-3">
                                                <label for="jam_kembali{{ $item->id }}" class="form-label">Jam
                                                    Kembali</label>
                                                <input type="time" class="form-control" id="jam_kembali{{ $item->id }}"
                                                    name="jam_kembali" value="{{ $item->jam_kembali }}" readonly>
                                            </div>
                                            <!-- Nama Driver -->
                                            <div class="col-md-6 mb-3">
                                                <label for="id_driver{{ $item->id }}" class="form-label">Nama
                                                    Driver</label>
                                                <select class="form-select select2" id="id_driver{{ $item->id }}"
                                                    name="id_driver" @if(Auth::user()->role !== 'hrga') disabled @endif>
                                                    <option value="">Pilih Driver</option>
                                                    @foreach($driver->where('status', 'Tersedia') as $d)
                                                    <option value="{{ $d->id }}" @if($item->id_driver == $d->id)
                                                        selected @endif>{{ $d->nama_driver }}</option>
                                                    @endforeach
                                                    @if($item->id_driver && !$driver->where('status', 'Tersedia')->contains('id', $item->id_driver))
                                                    <option value="{{ $item->id_driver }}" selected>
                                                        {{ $item->driver->nama_driver ?? 'Driver tidak ditemukan' }}
                                                    </option>
                                                    @endif
                                                </select>
                                            </div>
                                            <!-- Keterangan -->
                                            <div class="col-md-12 mb-3">
                                                <label for="keterangan{{ $item->id }}"
                                                    class="form-label">Keterangan</label>
                                                <textarea class="form-control" id="keterangan{{ $item->id }}"
                                                    name="keterangan" rows="2">{{ $item->keterangan }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
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
<script>
    // Fungsi untuk mendapatkan CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    }

    $(document).ready(function() {
        // Set nilai default tanggal ke hari ini jika tidak ada parameter di URL
        if (!window.location.search.includes('start_date') && !window.location.search.includes('end_date')) {
            const today = new Date().toISOString().split('T')[0];
            $('#start_date').val(today);
            $('#end_date').val(today);
            $('#filterForm').submit();
        }

        // Inisialisasi DataTable
        const table = $('#pemesananMobilTable').DataTable({
            order: [[1, 'asc']],
            scrollX: false,
            scrollY: 500,
            paging: false,
            scrollCollapse: true,
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

        // Event listener untuk input tanggal
        $('#start_date, #end_date').on('change', function() {
            if ($('#start_date').val() && $('#end_date').val()) {
                $('#filterSpinner').removeClass('d-none');
                $('#filterText').text('Memuat...');
                $('#filterForm').submit();
            }
        });

        // Event listener untuk form submit
        $('#filterForm').on('submit', function() {
            $('#filterSpinner').removeClass('d-none');
            $('#filterText').text('Memuat...');
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
        if ($('#selectAll').length > 0) {
            $('#selectAll').on('change', function() {
                const isChecked = $(this).prop('checked');
                $('.item-checkbox').prop('checked', isChecked);
                updateDeleteButtonVisibility();
            });
        }

        // Handle individual checkboxes
        $(document).on('change', '.item-checkbox', function() {
            updateSelectAllState();
            updateDeleteButtonVisibility();
        });

        // Function to update select all checkbox state
        function updateSelectAllState() {
            const totalCheckboxes = $('.item-checkbox').length;
            const checkedCheckboxes = $('.item-checkbox:checked').length;
            if ($('#selectAll').length > 0) {
                $('#selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes === checkedCheckboxes);
            }
        }

        // Function to update delete button visibility
        function updateDeleteButtonVisibility() {
            const checkedCount = $('.item-checkbox:checked').length;
            if ($('#deleteSelectedBtn').length > 0) {
                $('#deleteSelectedBtn').toggle(checkedCount > 0);
            }
        }

        // Handle delete selected button click
        if ($('#deleteSelectedBtn').length > 0) {
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
                text: 'Apakah Anda yakin ingin mengupdate jam berangkat?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, update jam berangkat',
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
        }

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
                text: 'Apakah Anda yakin ingin mengupdate jam kembali?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, update jam kembali',
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
                            jam_kembali: currentTime
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
                $(`#karyawan_id${itemId}`).closest('.form-karyawan').show();
                $(`#departemen${itemId}`).closest('.form-driveronly').hide();
            } else {
                $(`#karyawan_id${itemId}`).closest('.form-karyawan').hide();
                $(`#departemen${itemId}`).closest('.form-driveronly').show();
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

        // Handle approve manager button click
        $(document).on('click', '.btn-approve-manager', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyetujui surat jalan ini sebagai manager departemen?',
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

                    fetch(`/surat-jalan/${id}/approve-manager`, {
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
                                text: 'Surat jalan berhasil disetujui oleh manager',
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

        // Handle approve HRGA button click
        $(document).on('click', '.btn-approve-hrga', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengkonfirmasi surat jalan ini sebagai HRGA?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, konfirmasi',
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

                    // Kirim request dengan CSRF token
                    $.ajax({
                        url: `/surat-jalan/${id}/approve-hrga`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Surat jalan berhasil dikonfirmasi oleh HRGA',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message || 'Terjadi kesalahan saat mengkonfirmasi surat jalan'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat mengkonfirmasi surat jalan';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });

        // Tambahkan event click untuk menampilkan/menyembunyikan child row
        $('#pemesananMobilTable tbody').on('click', 'tr:not(.child-row)', function() {
            const id = $(this).data('id');
            const childRow = $(`tr.child-row[data-id="${id}"]`);

            if (childRow.length) {
                if (childRow.is(':visible')) {
                    childRow.hide();
                    $(this).removeClass('shown');
                } else {
                    childRow.show();
                    $(this).addClass('shown');
                }
            }
        });

        // Handle check security button click
        $(document).on('click', '.btn-check-security', function() {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyetujui surat jalan ini sebagai security?',
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

                    fetch(`/surat-jalan/${id}/check-security`, {
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
                                text: 'Surat jalan berhasil disetujui sebagai security',
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


        // Tambahkan style untuk row yang bisa diklik
        $('#pemesananMobilTable tbody tr:not(.child-row)').css('cursor', 'pointer');
    });
</script>
@endpush
