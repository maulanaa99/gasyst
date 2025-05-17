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
                        </div>
                        <input type="hidden" class="form-control" id="PIC" name="PIC" value="{{ auth()->user()->name }}"
                            required>
                        <input type="hidden" id="id_lokasi" name="id_lokasi" required>
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
                            <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="nama_lokasi" name="nama_lokasi" required>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#lokasiModal">
                                    <i class="icon-base ri ri-contacts-book-3-line"></i>
                                </button>
                            </div>
                            <input type="hidden" id="id_lokasi" name="id_lokasi">
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
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"
                                        id="submitLokasiSpinner"></span>
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
                                <td>{{ $l->nama_lokasi }}</td>
                                <td>{{ $l->alamat }}</td>
                                <td>{{ $l->keterangan }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary select-lokasi"
                                        data-id="{{ $l->id }}"
                                        data-nama="{{ $l->nama_lokasi }}"
                                        data-alamat="{{ $l->alamat }}">
                                        <i class="icon-base ri ri-check-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-lokasi"
                                        data-id="{{ $l->id }}"
                                        data-nama="{{ $l->nama_lokasi }}">
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
    // Script untuk toggle form karyawan/driver only
    document.querySelectorAll('input[name="jenis_pemesanan"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'karyawan') {
                document.getElementById('formKaryawan').style.display = 'block';
                document.getElementById('formDriverOnly').style.display = 'none';
            } else {
                document.getElementById('formKaryawan').style.display = 'none';
                document.getElementById('formDriverOnly').style.display = 'block';
            }
        });
    });

    // Script untuk form submission pemesanan mobil
    document.getElementById('addPemesananMobilForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validasi form
        const idLokasi = document.getElementById('id_lokasi').value;
        if (!idLokasi) {
            Swal.fire({
                title: 'Error!',
                text: 'Silakan pilih lokasi terlebih dahulu',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        const submitSpinner = document.getElementById('submitSpinner');
        const submitText = document.getElementById('submitText');

        // Disable button and show spinner
        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');
        submitText.textContent = 'Menyimpan...';

        // Submit form
        this.submit();
    });

    // Script untuk pencarian lokasi
    document.getElementById('btnSearchLokasi').addEventListener('click', function() {
        const searchTerm = document.getElementById('searchLokasi').value.toLowerCase();
        const rows = document.querySelectorAll('#lokasiTableBody tr');

        rows.forEach(row => {
            const namaLokasi = row.cells[0].textContent.toLowerCase();
            const alamatLokasi = row.cells[1].textContent.toLowerCase();
            const keterangan = row.cells[2].textContent.toLowerCase();

            if (namaLokasi.includes(searchTerm) ||
                alamatLokasi.includes(searchTerm) ||
                keterangan.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Script untuk memilih lokasi
    document.querySelectorAll('.select-lokasi').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const alamat = this.getAttribute('data-alamat');

            // Set nilai input nama lokasi dan id_lokasi
            document.getElementById('nama_lokasi').value = nama;
            document.getElementById('alamat').value = alamat;
            document.getElementById('id_lokasi').value = id;

            // Sembunyikan button tambah lokasi
            document.getElementById('submitLokasiBtn').style.display = 'none';

            // Tutup modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('lokasiModal'));
            modal.hide();

            // Tampilkan sweetalert
            Swal.fire({
                title: 'Berhasil!',
                text: 'Lokasi berhasil dipilih',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
    });

    // Script untuk menampilkan/menyembunyikan button tambah lokasi
    function checkLokasiButton() {
        const namaLokasi = document.getElementById('nama_lokasi').value;
        const alamat = document.getElementById('alamat').value;
        const idLokasi = document.getElementById('id_lokasi').value;
        const submitBtn = document.getElementById('submitLokasiBtn');

        // Jika ada id_lokasi (dipilih dari tabel), sembunyikan button
        if (idLokasi) {
            submitBtn.style.display = 'none';
        }
        // Jika form diisi manual (tidak ada id_lokasi), tampilkan button
        else if (namaLokasi || alamat) {
            submitBtn.style.display = 'block';
        }
        // Jika form kosong, sembunyikan button
        else {
            submitBtn.style.display = 'none';
        }
    }

    // Event listener untuk input fields
    document.getElementById('nama_lokasi').addEventListener('input', checkLokasiButton);
    document.getElementById('alamat').addEventListener('input', checkLokasiButton);

    // Script untuk submit form tambah lokasi
    document.getElementById('formLokasi').addEventListener('submit', function(e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitLokasiBtn');
        const submitSpinner = document.getElementById('submitLokasiSpinner');
        const submitText = document.getElementById('submitLokasiText');

        // Disable button and show spinner
        submitBtn.disabled = true;
        submitSpinner.classList.remove('d-none');
        submitText.textContent = 'Menyimpan...';

        // Submit form
        this.submit();
    });

    // Script untuk delete lokasi
    document.querySelectorAll('.delete-lokasi').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');

            if (confirm(`Apakah Anda yakin ingin menghapus lokasi "${nama}"?`)) {
                // Kirim request delete
                fetch(`/lokasi/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hapus baris dari tabel
                        this.closest('tr').remove();
                        // Tampilkan notifikasi sukses
                        toastr.success('Lokasi berhasil dihapus');
                    } else {
                        // Tampilkan notifikasi error
                        toastr.error(data.message || 'Gagal menghapus lokasi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Terjadi kesalahan saat menghapus lokasi');
                });
            }
        });
    });
</script>
@endpush
