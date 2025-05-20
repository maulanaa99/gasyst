@extends('layout.master')

@push('page-styles')
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Data Pemesanan Mobil</h5>
                </div>
                <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data"
                    id="addPemesananMobilForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label class="form-label">Jenis Pemesanan</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="jenis_pemesanan"
                                                id="jenisKaryawan" value="Karyawan" checked>
                                            <label class="form-check-label" for="jenisKaryawan">
                                                Karyawan
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="jenis_pemesanan"
                                                id="jenisDriverOnly" value="Driver Only">
                                            <label class="form-check-label" for="jenisDriverOnly">
                                                Driver Only
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="no_surat_jalan" class="form-label">Nomor Surat Jalan</label>
                                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan"
                                    value="SJ-{{ date('Ymd') }}-{{ str_pad(App\Models\SuratJalan::count() + 1, 4, '0', STR_PAD_LEFT) }}"
                                    readonly>
                            </div>
                            <div id="formKaryawan">
                                <div class="form-group mb-3">
                                    <label for="id_karyawan" class="form-label">Nama Karyawan</label>
                                    <div class="input-group">
                                        <select class="form-select select2" id="id_karyawan" name="karyawan_id[]"
                                            multiple required>
                                            @foreach($karyawan as $karyawans)
                                            <option value="{{ $karyawans->id }}">{{ $karyawans->nik }} | {{
                                                $karyawans->nama_karyawan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
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

                            <div class="form-group mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control h-px-100" id="keterangan" name="keterangan"
                                    required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label for="id_lokasi" class="form-label">Nama Lokasi</label>
                                <div class="input-group">
                                    <select class="form-select select2" id="id_lokasi" name="lokasi_id[]"
                                        multiple required>
                                        @foreach($lokasi as $lokasis)
                                        <option value="{{ $lokasis->id }}">{{ $lokasis->kode_lokasi }} | {{
                                            $lokasis->nama_lokasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" class="form-control" id="PIC" name="PIC" value="{{ auth()->user()->name }}"
                            required>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">Kembali</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                                id="submitSpinner"></span>
                            <span id="submitText">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Lokasi Baru</h5>
                </div>
                <form action="{{ route('lokasi.store') }}" method="POST" id="formLokasi">
                    @csrf
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="kode_lokasi" class="form-label">Kode Lokasi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="kode_lokasi" name="kode_lokasi" required>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#lokasiModal">
                                    <i class="icon-base ri ri-contacts-book-3-line"></i>
                                </button>
                            </div>
                            <input type="hidden" id="id_lokasi" name="id_lokasi">
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" required>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="alamat" class="form-label">Alamat Lokasi</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100" id="submitLokasiBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status"
                                        aria-hidden="true" id="submitLokasiSpinner"></span>
                                    <i class="icon-base ri ri-save-line ms-1"></i>
                                    <span id="submitLokasiText"> Simpan Lokasi</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lokasi -->
<div class="modal fade" id="lokasiModal" tabindex="-1" aria-labelledby="lokasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lokasiModalLabel">Daftar Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchLokasi" placeholder="Cari lokasi...">
                            <button class="btn btn-outline-primary" type="button" id="btnSearchLokasi">
                                <i class="icon-base ri ri-search-2-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Lokasi</th>
                                <th>Nama Lokasi</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="lokasiTableBody">
                            @foreach($lokasi as $l)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $l->kode_lokasi }}</td>
                                <td>{{ $l->nama_lokasi }}</td>
                                <td>{{ $l->alamat }}</td>
                                <td>{{ $l->keterangan }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary select-lokasi"
                                        data-id="{{ $l->id }}" data-kode="{{ $l->kode_lokasi }}"
                                        data-nama="{{ $l->nama_lokasi }}" data-alamat="{{ $l->alamat }}">
                                        <i class="icon-base ri ri-check-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-lokasi"
                                        data-id="{{ $l->id }}" data-nama="{{ $l->nama_lokasi }}">
                                        <i class="icon-base ri ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('page-script')
<script>
    $(document).ready(function() {
        // Script untuk toggle form karyawan/driver only
        $('input[name="jenis_pemesanan"]').on('change', function() {
            if (this.value === 'Karyawan') {
                $('#formKaryawan').show();
                $('#formDriverOnly').hide();
                $('#id_karyawan').prop('required', true);
                $('#id_departemen').prop('required', false);
            } else {
                $('#formKaryawan').hide();
                $('#formDriverOnly').show();
                $('#id_karyawan').prop('required', false);
                $('#id_departemen').prop('required', true);
            }
        });

        // Script untuk form submission pemesanan mobil
        $('#addPemesananMobilForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Form submission started');

            const formData = new FormData(this);
            console.log('Form data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            const jenisPemesanan = $('input[name="jenis_pemesanan"]:checked').val();
            const karyawanSelect = $('#id_karyawan');
            const departemenSelect = $('#id_departemen');
            const lokasiSelect = $('#id_lokasi');

            console.log('Jenis Pemesanan:', jenisPemesanan);
            console.log('Karyawan Value:', karyawanSelect.val());
            console.log('Departemen Value:', departemenSelect.val());
            console.log('Lokasi Value:', lokasiSelect.val());

            // Validasi berdasarkan jenis pemesanan
            if (jenisPemesanan === 'Karyawan') {
                if (!karyawanSelect.val()) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih karyawan terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            } else {
                if (!departemenSelect.val()) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih departemen terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }

            // Validasi lokasi
            if (!lokasiSelect.val()) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Silakan pilih lokasi terlebih dahulu',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const submitBtn = $('#submitBtn');
            const submitSpinner = $('#submitSpinner');
            const submitText = $('#submitText');

            // Disable button and show spinner
            submitBtn.prop('disabled', true);
            submitSpinner.removeClass('d-none');
            submitText.text('Menyimpan...');

            console.log('Submitting form...');
            // Submit form
            this.submit();
        });

        // Inisialisasi select2
        $('#id_karyawan').select2({
            width: '100%',
            placeholder: 'Pilih Karyawan'
        });

        $('#id_departemen').select2({
            width: '100%',
            placeholder: 'Pilih Departemen'
        });

        $('#id_lokasi').select2({
            width: '100%',
            placeholder: 'Pilih Lokasi'
        });

        // Script untuk pencarian lokasi
        $('#btnSearchLokasi').on('click', function() {
            const searchTerm = $('#searchLokasi').val().toLowerCase();
            $('#lokasiTableBody tr').each(function() {
                const namaLokasi = $(this).find('td:eq(1)').text().toLowerCase();
                const alamatLokasi = $(this).find('td:eq(2)').text().toLowerCase();
                const keterangan = $(this).find('td:eq(3)').text().toLowerCase();

                if (namaLokasi.includes(searchTerm) ||
                    alamatLokasi.includes(searchTerm) ||
                    keterangan.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Script untuk memilih lokasi
        $('.select-lokasi').on('click', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');
            const nama = $(this).data('nama');
            const alamat = $(this).data('alamat');

            $('#kode_lokasi').val(kode);
            $('#nama_lokasi').val(nama);
            $('#alamat').val(alamat);
            $('#id_lokasi').val(id);

            $('#submitLokasiBtn').hide();

            $('#lokasiModal').modal('hide');

            Swal.fire({
                title: 'Berhasil!',
                text: 'Lokasi berhasil dipilih',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });

        // Script untuk delete lokasi
        $('.delete-lokasi').on('click', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            if (confirm(`Apakah Anda yakin ingin menghapus lokasi "${nama}"?`)) {
                $.ajax({
                    url: `/lokasi/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    success: function(data) {
                        if (data.success) {
                            $(this).closest('tr').remove();
                            toastr.success('Lokasi berhasil dihapus');
                        } else {
                            toastr.error(data.message || 'Gagal menghapus lokasi');
                        }
                    }.bind(this),
                    error: function(error) {
                        console.error('Error:', error);
                        toastr.error('Terjadi kesalahan saat menghapus lokasi');
                    }
                });
            }
        });
    });
</script>
@endpush
