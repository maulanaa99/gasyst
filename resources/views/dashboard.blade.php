@extends('layout.master')

@push('page-styles')
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-6">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-primary"><i
                                    class="icon-base ri ri-car-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $totalPemesananMobil }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Pemesanan Mobil</h6>
                    <p class="mb-0">
                        <small class="text-muted" id="percentageChange"></small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-warning"><i
                                    class="icon-base ri ri-alert-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $DriverAvailable }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Available Driver</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">(ERR#!)</span>
                        <small class="text-body-secondary">than last week</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-danger"><i
                                    class="icon-base ri ri-route-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">{{ $notAvailableDriver }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Not Available Driver</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">(ERR#!)</span>
                        <small class="text-body-secondary">than last week</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-info"><i
                                    class="icon-base ri ri-time-line icon-24px"></i></span>
                        </div>
                        <h4 class="mb-0">13</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Late vehicles</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">-2.5%</span>
                        <small class="text-body-secondary">than last week</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-8 col-md-6">
            <div class="card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th class="text-truncate">Nama Driver</th>
                                <th class="text-truncate">Plat No</th>
                                <th class="text-truncate">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($driver as $item)
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-start align-items-center user-name">
                                        <div class="avatar-wrapper">
                                            <div class="avatar avatar-s me-2"><span
                                                    class="avatar-initial rounded-circle bg-primary">{{
                                                    $item->nama_driver[0] }}</span></div>
                                        </div>
                                        <div class="d-flex flex-column"><span
                                                class="emp_name text-truncate text-heading fw-medium">{{
                                                $item->nama_driver }}</span><small class="emp_post text-truncate">{{
                                                $item->outsourching }}</small></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column"><span class="emp_name text-truncate">
                                            {{$item->mobils->nama_mobil }}</span>
                                        <small class="emp_post text-truncate">
                                            {{$item->mobils->plat_no }}</small>
                                    </div>
                                </td>
                                @if ($item->status == 'Available')
                                <td><span class="badge rounded-pill  bg-label-success">{{ $item->status }}</span>
                                </td>
                                @else
                                <td><span class="badge rounded-pill  bg-label-danger">{{ $item->status }}</span></td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Pemesanan Mobil -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0">Statistik Pemesanan Mobil</h5>

                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary waves-effect"
                            id="selectedMonth">Januari</button>
                        <button type="button"
                            class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="1">Januari</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="2">Februari</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="3">Maret</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="4">April</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-month="5">Mei</a>
                            </li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-month="6">Juni</a>
                            </li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);" data-month="7">Juli</a>
                            </li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="8">Agustus</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="9">September</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="10">Oktober</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="11">November</a></li>
                            <li><a class="dropdown-item waves-effect" href="javascript:void(0);"
                                    data-month="12">Desember</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="pemesananMobilChart" style="min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>




</div>
@endsection

@push('page-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    let chart;

    // Inisialisasi chart
    function initChart(data) {
        const options = {
            series: [{
                name: 'Total Pemesanan',
                data: data.values
            }],
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 5
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: data.labels,
                title: {
                    text: 'Tanggal'
                }
            },
            yaxis: {
                title: {
                    text: 'Jumlah Pemesanan'
                }
            },
            fill: {
                opacity: 1,
                colors: ['#696cff']
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Pemesanan"
                    }
                }
            },
            legend: {
                show: true,
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                fontSize: '15px',
                fontFamily: 'var(--bs-font-family-base)',
                fontWeight: 400,
                markers: {
                    width: 8,
                    height: 8,
                    strokeWidth: 0,
                    strokeColor: '#fff',
                    radius: 12,
                    offsetX: 0,
                    offsetY: 0
                },
                itemMargin: {
                    horizontal: 8,
                    vertical: 0
                },
                onItemClick: {
                    toggleDataSeries: true
                },
                onItemHover: {
                    highlightDataSeries: true
                }
            }
        };

        if (chart) {
            chart.destroy();
        }

        chart = new ApexCharts(document.querySelector("#pemesananMobilChart"), options);
        chart.render();

        // Update persentase perubahan
        const percentageChange = data.percentageChange;
        const percentageElement = document.getElementById('percentageChange');
        if (percentageChange > 0) {
            percentageElement.innerHTML = `<span class="text-success">+${percentageChange}%</span> dari minggu lalu`;
        } else if (percentageChange < 0) {
            percentageElement.innerHTML = `<span class="text-danger">${percentageChange}%</span> dari minggu lalu`;
        } else {
            percentageElement.innerHTML = `<span class="text-muted">0%</span> dari minggu lalu`;
        }
    }

    // Fungsi untuk mengambil data berdasarkan periode
    function fetchData(period) {
        fetch(`/api/pemesanan-mobil/${period}`)
            .then(response => response.json())
            .then(data => {
                initChart(data);
            })
            .catch(error => console.error('Error:', error));
    }

    // Event listener untuk dropdown items
    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            const month = this.getAttribute('data-month');
            const monthName = this.textContent;
            document.getElementById('selectedMonth').textContent = monthName;
            fetchData(`bulan-${month}`);
        });
    });

    // Set nilai default ke bulan ini
    const currentMonth = new Date().getMonth() + 1;
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    document.getElementById('selectedMonth').textContent = monthNames[currentMonth - 1];

    // Load data bulan ini secara default
    fetchData(`bulan-${currentMonth}`);
});
</script>
@endpush