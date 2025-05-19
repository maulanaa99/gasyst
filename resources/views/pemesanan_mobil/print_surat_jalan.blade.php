<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Jalan - {{ $suratJalan->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            margin-bottom: 5px;
        }
        .document-number {
            font-size: 14px;
        }
        .content {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">PT. GASYST</div>
        <div class="document-title">SURAT JALAN</div>
        <div class="document-number">No: {{ str_pad($suratJalan->id, 4, '0', STR_PAD_LEFT) }}/SJ/{{ date('Y') }}</div>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">Informasi Pemesanan</div>
            <div class="info-row">
                <div class="info-label">Tanggal</div>
                <div class="info-value">: {{ date('d/m/Y', strtotime($suratJalan->tanggal)) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Pemesanan</div>
                <div class="info-value">: {{ $suratJalan->id_karyawan ? 'Karyawan' : 'Driver Only' }}</div>
            </div>
            @if($suratJalan->id_karyawan)
            <div class="info-row">
                <div class="info-label">Nama Karyawan</div>
                <div class="info-value">: {{ $suratJalan->karyawan->nama_karyawan }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Departemen</div>
                <div class="info-value">: {{ $suratJalan->karyawan->departemen }}</div>
            </div>
            @else
            <div class="info-row">
                <div class="info-label">Departemen</div>
                <div class="info-value">: {{ $suratJalan->departemen->nama_departemen }}</div>
            </div>
            @endif
        </div>

        <div class="section">
            <div class="section-title">Informasi Perjalanan</div>
            <div class="info-row">
                <div class="info-label">Tujuan</div>
                <div class="info-value">: {{ $suratJalan->lokasi->nama_lokasi }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat</div>
                <div class="info-value">: {{ $suratJalan->lokasi->alamat }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jam Berangkat</div>
                <div class="info-value">: {{ $suratJalan->jam_berangkat_aktual ?? $suratJalan->jam_berangkat }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jam Kembali</div>
                <div class="info-value">: {{ $suratJalan->jam_kembali_aktual ?? $suratJalan->jam_kembali }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Driver</div>
                <div class="info-value">: {{ $suratJalan->driver->nama_driver ?? '-' }}</div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Keterangan</div>
            <div class="info-value">{{ $suratJalan->keterangan }}</div>
        </div>
    </div>

    <div class="footer">
        <div class="signature-box">
            <div>Dibuat oleh,</div>
            <div style="position: relative; height: 120px;">
                @if(Auth::user()->signature)
                    <div style="position: relative; height: 80px;">
                        <div style="height: 120%; background-image: url('{{ route('signature.show', 'maulana.png') }}'); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg); text-align: center; font-size: 12px; color: #000; background-color: rgba(255, 255, 255, 0.7); padding: 2px 5px; border-radius: 3px; white-space: nowrap;">
                            {{ date('d/m/Y H:i:s') }}
                        </div>
                    </div>
                @endif
                <div style="width: 100%; border-top: 1px solid #000; margin-top: 10px;"></div>
                <div style="text-align: center; font-size: 12px; margin-top: 5px;">
                    {{ $suratJalan->PIC }}
                </div>
            </div>
        </div>
        <div class="signature-box">
            <div>Diketahui oleh,</div>
            <div style="position: relative; height: 120px;">
                @if($suratJalan->status_approve)
                    <div style="position: relative; height: 80px;">
                        @if($suratJalan->approvedBy && $suratJalan->approvedBy->signature)
                            <div style="height: 150%; background-image: url('{{ route('signature.show', basename($suratJalan->approvedBy->signature)) }}'); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
                        @endif
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg); text-align: center; font-size: 12px; color: #000; background-color: rgba(255, 255, 255, 0.7); padding: 2px 5px; border-radius: 3px; white-space: nowrap;">
                            {{ $suratJalan->approved_at ? date('d/m/Y H:i:s', strtotime($suratJalan->approved_at)) : '' }}
                        </div>
                        <div style="position: absolute; top: 50%; left: 30%; transform: translate(-50%, -50%); width: 100px; height: 100px; background-image: url('{{ route('security.cap') }}'); background-size: contain; background-position: center; background-repeat: no-repeat;"></div>
                    </div>
                @endif
                <div style="width: 100%; border-top: 1px solid #000; margin-top: 10px;"></div>
                <div style="text-align: center; font-size: 12px; margin-top: 5px;">
                    Security
                </div>
            </div>
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Cetak Surat Jalan
        </button>
    </div>
</body>
</html>
