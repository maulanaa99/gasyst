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
                                <input type="text" class="form-control" id="no_surat_jalan" name="no_surat_jalan" disabled>
                            </div>
                            <div id="formKaryawan">
                                <div class="form-group mb-3">
                                    <label for="id_karyawan" class="form-label">Nama Karyawan</label>
                                    <div class="input-group">
                                        <select class="form-select select2" id="id_karyawan" name="karyawan_id[]"
                                            multiple required>
                                            @foreach($karyawan as $karyawans)
                                            <option value="{{ $karyawans->id }}">{{ $karyawans->NIK }} | {{
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
                                        @foreach($departemen as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->nama_departemen }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Departemen harus dipilih</div>
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
                            <div class="invalid-feedback">Kode lokasi harus diisi</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" required>
                            </div>
                            <div class="invalid-feedback">Nama lokasi harus diisi</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="alamat" class="form-label">Alamat Lokasi</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
                            <div class="invalid-feedback">Alamat lokasi harus diisi</div>
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
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px">No</th>
                                <th>Kode Lokasi</th>
                                <th>Nama Lokasi</th>
                                <th>Alamat</th>
                                <th>Keterangan</th>
                                <th class="text-center" style="width: 100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="lokasiTableBody">
                            @forelse($lokasi as $l)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $l->kode_lokasi }}</td>
                                <td>{{ $l->nama_lokasi }}</td>
                                <td>{{ $l->alamat }}</td>
                                <td>{{ $l->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-inline-block">
                                        <button type="button" class="btn btn-sm btn-primary select-lokasi"
                                            data-id="{{ $l->id }}"
                                            data-kode="{{ $l->kode_lokasi }}"
                                            data-nama="{{ $l->nama_lokasi }}"
                                            data-alamat="{{ $l->alamat }}"
                                            data-bs-toggle="tooltip"
                                            title="Pilih Lokasi">
                                            <i class="icon-base ri ri-check-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-lokasi"
                                            data-id="{{ $l->id }}"
                                            data-nama="{{ $l->nama_lokasi }}"
                                            data-bs-toggle="tooltip"
                                            title="Hapus Lokasi">
                                            <i class="icon-base ri ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data lokasi</td>
                            </tr>
                            @endforelse
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
        // Fungsi untuk mendapatkan nomor surat jalan
        function getNextNumber(tanggal = null) {
            console.log('Mendapatkan nomor surat jalan untuk tanggal:', tanggal);
            const currentDate = tanggal || $('#tanggal').val();

            if (!currentDate) {
                console.error('Tanggal tidak tersedia');
                Swal.fire({
                    title: 'Error!',
                    text: 'Tanggal harus diisi terlebih dahulu',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            $.ajax({
                url: '{{ route("surat-jalan.get-next-number") }}',
                type: 'GET',
                data: {
                    tanggal: currentDate
                },
                success: function(response) {
                    console.log('Response nomor surat jalan:', response);
                    if (response.success) {
                        $('#no_surat_jalan').val(response.no_surat_jalan);
                        // Enable submit button jika sebelumnya disabled
                        $('#submitBtn').prop('disabled', false);
                    } else {
                        console.error('Gagal mendapatkan nomor surat jalan:', response.message);
                        Swal.fire({
                            title: 'Error!',
                            text: response.message || 'Gagal mendapatkan nomor surat jalan',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        // Disable submit button jika gagal mendapatkan nomor
                        $('#submitBtn').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    console.error('Error saat mendapatkan nomor surat jalan:', xhr);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mendapatkan nomor surat jalan',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    // Disable submit button jika terjadi error
                    $('#submitBtn').prop('disabled', true);
                }
            });
        }

        // Panggil fungsi saat halaman dimuat
        getNextNumber();

        // Event handler untuk perubahan tanggal
        $('#tanggal').on('change', function() {
            const submitBtn = $('#submitBtn');
            submitBtn.prop('disabled', true); // Disable submit button saat tanggal berubah
            getNextNumber($(this).val());
        });

        // Script untuk toggle form karyawan/driver only
        $('input[name="jenis_pemesanan"]').on('change', function() {
            console.log('Jenis pemesanan berubah:', this.value);
            if (this.value === 'Karyawan') {
                $('#formKaryawan').show();
                $('#formDriverOnly').hide();
                $('#id_karyawan').prop('required', true);
                $('#id_departemen').prop('required', false);
                $('#id_departemen').val('').trigger('change');
            } else {
                $('#formKaryawan').hide();
                $('#formDriverOnly').show();
                $('#id_karyawan').prop('required', false);
                $('#id_departemen').prop('required', true);
                $('#id_karyawan').val('').trigger('change');
            }
        });

        // Script untuk form submission pemesanan mobil
        $('#addPemesananMobilForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Form submission dimulai');

            const formData = new FormData(this);
            const jenisPemesanan = $('input[name="jenis_pemesanan"]:checked').val();
            const karyawanSelect = $('#id_karyawan');
            const departemenSelect = $('#id_departemen');
            const lokasiSelect = $('#id_lokasi');
            const noSuratJalan = $('#no_surat_jalan').val();

            // Validasi no surat jalan
            if (!noSuratJalan) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Nomor surat jalan belum tersedia. Mohon tunggu sebentar atau refresh halaman.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Log data yang akan dikirim
            console.log('Data yang akan dikirim:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Validasi berdasarkan jenis pemesanan
            if (jenisPemesanan === 'Driver Only') {
                if (!departemenSelect.val()) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih departemen terlebih dahulu',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            } else {
                if (!karyawanSelect.val()) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Silakan pilih karyawan terlebih dahulu',
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

            // Jika validasi berhasil, submit form
            const submitBtn = $('#submitBtn');
            const submitSpinner = $('#submitSpinner');
            const submitText = $('#submitText');

            // Disable button dan tampilkan spinner
            submitBtn.prop('disabled', true);
            submitSpinner.removeClass('d-none');
            submitText.text('Menyimpan...');

            // Siapkan data untuk dikirim
            const data = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                tanggal: formData.get('tanggal'),
                no_surat_jalan: noSuratJalan,
                jam_berangkat: formData.get('jam_berangkat'),
                jam_kembali: formData.get('jam_kembali'),
                driver_id: formData.get('driver_id'),
                keterangan: formData.get('keterangan'),
                jenis_pemesanan: jenisPemesanan,
                lokasi_id: lokasiSelect.val()
            };

            // Tambahkan data sesuai jenis pemesanan
            if (jenisPemesanan === 'Driver Only') {
                data.id_departemen = departemenSelect.val();
            } else {
                data.karyawan_id = karyawanSelect.val();
            }

            console.log('Data yang akan dikirim:', data);

            // Kirim form dengan AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: data,
                success: function(response) {
                    console.log('Response:', response);
                    if (response.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.href = '{{ route("surat-jalan.index") }}';
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: response.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseJSON);
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';

                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('\n');
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error'
                    });
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitSpinner.addClass('d-none');
                    submitText.text('Simpan');
                }
            });
        });

        // Inisialisasi select2
        $('#id_karyawan').select2({
            width: '100%',
            placeholder: 'Pilih Karyawan'
        }).on('change', function() {
            console.log('Karyawan dipilih:', $(this).val());
        });

        $('#id_departemen').select2({
            width: '100%',
            placeholder: 'Pilih Departemen',
            allowClear: true
        }).on('change', function() {
            console.log('Departemen dipilih:', $(this).val());
        });

        $('#id_lokasi').select2({
            width: '100%',
            placeholder: 'Pilih Lokasi'
        }).on('change', function() {
            console.log('Lokasi dipilih:', $(this).val());
        });

        // Inisialisasi tooltip
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Script untuk pencarian lokasi
        $('#btnSearchLokasi').on('click', function() {
            console.log('Mencari lokasi dengan keyword:', $('#searchLokasi').val());
            searchLokasi();
        });

        $('#searchLokasi').on('keyup', function(e) {
            if (e.key === 'Enter') {
                console.log('Mencari lokasi dengan keyword (Enter):', $(this).val());
                searchLokasi();
            }
        });

        function searchLokasi() {
            const searchTerm = $('#searchLokasi').val().toLowerCase();
            console.log('Mencari lokasi:', searchTerm);
            let found = false;

            $('#lokasiTableBody tr').each(function() {
                const kodeLokasi = $(this).find('td:eq(1)').text().toLowerCase();
                const namaLokasi = $(this).find('td:eq(2)').text().toLowerCase();
                const alamat = $(this).find('td:eq(3)').text().toLowerCase();
                const keterangan = $(this).find('td:eq(4)').text().toLowerCase();

                if (kodeLokasi.includes(searchTerm) ||
                    namaLokasi.includes(searchTerm) ||
                    alamat.includes(searchTerm) ||
                    keterangan.includes(searchTerm)) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });

            console.log('Hasil pencarian:', found ? 'Ditemukan' : 'Tidak ditemukan');

            if (!found) {
                $('#lokasiTableBody').append(`
                    <tr id="noResults">
                        <td colspan="6" class="text-center">Tidak ada data yang ditemukan</td>
                    </tr>
                `);
            } else {
                $('#noResults').remove();
            }
        }

        // Script untuk memilih lokasi
        $('.select-lokasi').on('click', function() {
            const id = $(this).data('id');
            const kode = $(this).data('kode');
            const nama = $(this).data('nama');
            const alamat = $(this).data('alamat');

            console.log('Lokasi dipilih:', { id, kode, nama, alamat });

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

            console.log('Mencoba menghapus lokasi:', { id, nama });

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus lokasi "${nama}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Konfirmasi hapus lokasi diterima');
                    $.ajax({
                        url: `/lokasi/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Response hapus lokasi:', response);
                            if (response.success) {
                                $(this).closest('tr').fadeOut(300, function() {
                                    $(this).remove();
                                    if ($('#lokasiTableBody tr').length === 0) {
                                        $('#lokasiTableBody').append(`
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data lokasi</td>
                                            </tr>
                                        `);
                                    }
                                });
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Lokasi berhasil dihapus',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                console.error('Gagal menghapus lokasi:', response.message);
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Gagal menghapus lokasi',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        }.bind(this),
                        error: function(xhr) {
                            console.error('Error saat menghapus lokasi:', xhr);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus lokasi',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                } else {
                    console.log('Konfirmasi hapus lokasi dibatalkan');
                }
            });
        });

        // Script untuk form lokasi
        $('#formLokasi').on('submit', function(e) {
            e.preventDefault();
            console.log('Form lokasi submission dimulai');

            // Validasi form
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                console.error('Validasi form lokasi gagal');
                return;
            }

            // Validasi input
            const kodeLokasi = $('#kode_lokasi').val().trim();
            const namaLokasi = $('#nama_lokasi').val().trim();
            const alamat = $('#alamat').val().trim();

            console.log('Data lokasi yang akan disimpan:', {
                kodeLokasi,
                namaLokasi,
                alamat
            });

            if (!kodeLokasi || !namaLokasi || !alamat) {
                console.error('Validasi input lokasi gagal:', {
                    kodeLokasi,
                    namaLokasi,
                    alamat
                });
                Swal.fire({
                    title: 'Error!',
                    text: 'Semua field harus diisi',
                    icon: 'error'
                });
                return;
            }

            // Konfirmasi sebelum simpan
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan lokasi baru?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Konfirmasi simpan lokasi diterima');
                    const submitBtn = $('#submitLokasiBtn');
                    const submitSpinner = $('#submitLokasiSpinner');
                    const submitText = $('#submitLokasiText');

                    // Disable button dan tampilkan spinner
                    submitBtn.prop('disabled', true);
                    submitSpinner.removeClass('d-none');
                    submitText.text('Menyimpan...');

                    // Siapkan data
                    const formData = new FormData(this);
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    console.log('Data form lokasi yang akan dikirim:', Object.fromEntries(formData));

                    // Kirim form
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log('Response simpan lokasi:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Lokasi berhasil disimpan',
                                    icon: 'success'
                                }).then(() => {
                                    // Reset form
                                    $('#formLokasi')[0].reset();
                                    $('#formLokasi').removeClass('was-validated');

                                    // Refresh data lokasi di select
                                    refreshLokasiSelect();
                                });
                            } else {
                                console.error('Gagal menyimpan lokasi:', response.message);
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.message || 'Gagal menyimpan lokasi',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error saat menyimpan lokasi:', {
                                status,
                                error,
                                response: xhr.responseText,
                                statusCode: xhr.status
                            });

                            let errorMessage = 'Terjadi kesalahan saat menyimpan lokasi';

                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }

                            Swal.fire({
                                title: 'Error!',
                                text: errorMessage,
                                icon: 'error'
                            });
                        },
                        complete: function() {
                            // Enable button dan sembunyikan spinner
                            submitBtn.prop('disabled', false);
                            submitSpinner.addClass('d-none');
                            submitText.text('Simpan Lokasi');
                        }
                    });
                } else {
                    console.log('Konfirmasi simpan lokasi dibatalkan');
                }
            });
        });

        // Fungsi untuk refresh data lokasi di select
        function refreshLokasiSelect() {
            try {
                console.log('Memperbarui daftar lokasi di select...');
                const select = $('#id_lokasi');
                select.empty();

                @foreach($lokasi as $l)
                    select.append(new Option(
                        `{{ $l->kode_lokasi }} | {{ $l->nama_lokasi }}`,
                        `{{ $l->id }}`
                    ));
                @endforeach

                select.trigger('change');
                console.log('Daftar lokasi berhasil diperbarui');
            } catch (error) {
                console.error('Error saat memperbarui daftar lokasi:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memperbarui daftar lokasi',
                    icon: 'error'
                });
            }
        }
    });
</script>
@endpush
