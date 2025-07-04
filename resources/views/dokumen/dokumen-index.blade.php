@extends('layout.master')

@push('page-styles')

@endpush

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row card-header mx-0 px-2">
            <h5 class="card-title mb-0 text-md-start text-center">List Dokumen</h5>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dokumenTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Diterima</th>
                        <th>Nama Pengirim</th>
                        <th>Nama Penerima</th>
                        <th>No Resi</th>
                        <th>Ekspedisi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dokumens as $dokumen)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $dokumen->tanggal_diterima }}</td>
                        <td>{{ $dokumen->nama_pengirim_dokumen }}</td>
                        <td>{{ $dokumen->nama_penerima_dokumen }}</td>
                        <td>{{ $dokumen->no_resi }}</td>
                        <td>{{ $dokumen->ekspedisi }}</td>
                        <td>{{ $dokumen->keterangan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')

@endpush
