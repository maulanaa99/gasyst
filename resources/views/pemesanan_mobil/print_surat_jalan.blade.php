<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Permission In/Out Office Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 70px;
        }

        .company-title {
            font-size: 24px;
            font-weight: bold;
            margin-left: 10px;
        }

        .form-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-table,
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .form-table td,
        .signature-table td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }

        .checkbox {
            width: 22px;
            height: 22px;
            border: 2px solid #000;
            margin-top: 5px;
            display: inline-block;
            margin-right: 5px;
            vertical-align: middle;
        }

        .section-title {
            font-weight: bold;
            margin-top: 15px;
        }

        .remarks {
            border: 1px solid #000;
            height: 40px;
            margin-bottom: 10px;
        }

        .footer-table {
            width: 100%;
            margin-top: 30px;
        }

        .footer-table td {
            border: 1px solid #000;
            height: 60px;
            text-align: center;
            vertical-align: bottom;
        }

        .small {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ asset('storage/SRI.jpg') }}" class="logo" alt="Logo">
        <div class="company-title">PT. SAKAE RIKEN INDONESIA</div>
    </div>
    <div class="form-title">PERMISSION IN/OUT OFFICE FORM</div>
    <table class="form-table">
        <tr>
            <td style="width: 10%;"><b>NIK</b></td>
            <td style="width: 40%;">
                @if ($suratJalan->karyawans->isNotEmpty())
                {{ $suratJalan->karyawans->pluck('NIK')->join(', ') }}
                @else
                -
                @endif
            </td>
            <td style="width: 10%;"><b>DEPT/SECT</b></td>
            <td style="width: 25%;">
                @if ($suratJalan->karyawans->isNotEmpty())
                {{ $suratJalan->karyawans->pluck('departemen.nama_departemen')->unique()->join(', ') }}
                @elseif ($suratJalan->suratJalanDetail->isNotEmpty() &&
                $suratJalan->suratJalanDetail->first()->departemen)
                {{ $suratJalan->suratJalanDetail->first()->departemen->nama_departemen }}
                @else
                -
                @endif
            </td>
        </tr>
        <tr>
            <td><b>NAME</b></td>
            <td>
                @if ($suratJalan->karyawans->isNotEmpty())
                {{ $suratJalan->karyawans->pluck('nama_karyawan')->join(', ') }}
                @else
                -
                @endif
            </td>
            <td><b>DATE</b></td>
            <td>{{ date('d-m-Y', strtotime($suratJalan->issued_at)) }}</td>
        </tr>
    </table>
    <div class="section-title" style="font-size: 11px;">Remarks (Destination/Reason)
        <hr>
    <table>
        <tr>
            <td style="font-size: 11px;"><b> Destination :</b></td>
            <td style="font-size: 11px;">{{ $suratJalan->lokasis->pluck('nama_lokasi')->join(', ') }}</td>
        </tr>
        <tr>
            <td style="font-size: 11px;"><b>Reason :</b></td>
            <td style="font-size: 11px;">{{ $suratJalan->keterangan }}</td>
        </tr>
    </table>
    <hr>
    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td>
                <div class="checkbox" style="background-color: #000;"></div> Out Office (Official)<br>
                <div class="checkbox"></div> Home Early
            </td>
            <td>
                <div class="checkbox"></div> Late<br>
                <div class="checkbox"></div> Others
            </td>
        </tr>
    </table>
    <table class="form-table">
        <tr>
            <td style="width: 25%;"><b>Leave Time</b></td>
            <td style="width: 25%;">{{ $suratJalan->jam_berangkat }}</td>
            <td style="width: 25%;"><b>Start Time</b></td>
            <td style="width: 25%;">{{ $suratJalan->jam_berangkat }}</td>
        </tr>
        <tr>
            <td><b>Return Time</b></td>
            <td>{{ $suratJalan->jam_kembali }}</td>
            <td><b>Car Type</b></td>
            <td>{{ $suratJalan->driver->mobil->nama_mobil ?? '-' }}</td>
        </tr>
        <tr>
            <td><b>Driver Name</b></td>
            <td>{{ $suratJalan->driver->nama_driver ?? '-' }}</td>
            <td><b>Number of vehicles</b></td>
            <td>{{ $suratJalan->driver->mobil->plat_no ?? '-' }}</td>
        </tr>

    </table>
    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <table class="signature-table" style="width: 40%;">
            <tr>
                <td><b>Issued by</b></td>
                <td><b>Approved by</b></td>
            </tr>
            <tr>
                <td
                    style="height: 70px; text-align: center; vertical-align: middle; position: relative; background-repeat: no-repeat; background-position: center; background-size: contain; @if(!empty($suratJalan->issuedBy->signature))background-image: url('{{ asset('storage/' . $suratJalan->issuedBy->signature) }}');@endif">
                    {{-- Signature sebagai background --}}
                    @if(!empty($suratJalan->issued_at))
                    <div
                        style="position: absolute; bottom: 2px; left: 0; width: 100%; font-size: 10px; color: #888; text-align: center;">
                        {{ date('d-m-Y H:i', strtotime($suratJalan->issued_at)) }}
                    </div>
                    @endif
                </td>
                <td
                    style="height: 70px; text-align: center; vertical-align: middle; position: relative; background-repeat: no-repeat; background-position: center; background-size: contain; @if(!empty($suratJalan->approveBy->signature))background-image: url('{{ asset('storage/' . $suratJalan->approveBy->signature) }}');@endif">
                    {{-- Signature sebagai background --}}
                    @if(!empty($suratJalan->approve_at))
                    <div
                        style="position: absolute; bottom: 2px; left: 0; width: 100%; font-size: 10px; color: #888; text-align: center;">
                        {{ date('d-m-Y H:i', strtotime($suratJalan->approve_at)) }}
                    </div>
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ $suratJalan->issuedBy->name ?? '' }}</td>
                <td>{{ $suratJalan->approveBy->name ?? '' }}</td>
            </tr>
        </table>
        <table class="signature-table" style="width: 40%;">
            <tr>
                <td><b>Checked by</b></td>
                <td><b>Confirmed by</b></td>
            </tr>
            <tr>
                <td
                    style="height: 70px; text-align: center; vertical-align: middle; position: relative; background-repeat: no-repeat; background-position: 60% center; background-size: contain; @if(!empty($suratJalan->checkedBy)) background-image: url('{{ asset('storage/Cap SR.png') }}'); @endif">
                    @if(!empty($suratJalan->checkedBy->signature))
                    <img src="{{ asset('storage/' . $suratJalan->checkedBy->signature) }}" alt="Signature Checked By"
                        style="max-height: 40px; display: block; margin: 0 auto; position: relative; z-index: 2;">
                    @endif
                    @if(!empty($suratJalan->checked_at))
                    <div
                        style="position: absolute; bottom: 2px; left: 0; width: 100%; font-size: 10px; color: #888; text-align: center;">
                        {{ date('d-m-Y H:i', strtotime($suratJalan->checked_at)) }}
                    </div>
                    @endif
                </td>
                <td style="height: 70px; text-align: center; vertical-align: middle; position: relative; background-repeat: no-repeat; background-position: center; background-size: contain;
                    @if(!empty($suratJalan->acknowledgedBy->signature))
                    background-image: url('{{ asset('storage/' . $suratJalan->acknowledgedBy->signature) }}');
                    @endif">
                    @if(!empty($suratJalan->acknowledged_at))
                    <div
                        style="position: absolute; bottom: 2px; left: 0; width: 100%; font-size: 10px; color: #888; text-align: center;">
                        {{ date('d-m-Y H:i', strtotime($suratJalan->acknowledged_at)) }}
                    </div>
                    @endif

                </td>
            </tr>
            <tr>
                <td>{{ $suratJalan->checkedBy->name ?? '' }}</td>
                <td>{{ $suratJalan->acknowledgedBy->name ?? '' }}</td>
            </tr>
        </table>
    </div>
</body>

</html>