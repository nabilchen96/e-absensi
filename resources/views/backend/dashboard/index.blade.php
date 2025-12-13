@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Dashboard</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <div class="card shadow">
                <div class="card-body">
                    <div id="grafikAbsen" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mt-4">
            <div class="card shadow">
                <div class="card-body">
                    <div id="grafikIzinCuti" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mt-4">
            <div class="card w-100 shadow" style="height: 500px !important;">
                <div class="card-body">
                    <b>
                        <span
                            style="font-size: 1.2em !important;
                        font-weight: bold !important;
                        fill: rgb(51, 51, 51); !important">
                            DATA PEGAWAI IZIN HARI INI
                        </span>
                    </b>
                    <div class="table-responsive mt-3" style="max-height: 400px; overflow-y: auto;">
                        <table id="myTable" class="table-bordered table table-striped" style="width: 100%;">
                            <thead style="position: sticky; top: 0;" class="bg-info text-white">
                                <tr>
                                    <th width="50%">User</th>
                                    <th>Keterangan</th>
                                    <th width="5px">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($izin as $i)
                                    <tr>
                                        <td style="vertical-align: top !important;">
                                            <b>Nama: </b><br>
                                            {{ $i->name }}<br><br>

                                            <b>NIP: </b><br>
                                            {{ $i->nip }} <br><br>

                                            <b>Unit Kerja: </b><br>
                                            {{ $i->satuan_kerja }}<br><br>
                                        </td>
                                        <td style="vertical-align: top !important;">
                                            <b>Jenis: </b><br>
                                            {{ $i->jenis }}<br><br>

                                            <b>Keterangan: </b><br>
                                            {{ $i->keterangan }}<br><br>
                                        </td>
                                        <td style="vertical-align: top !important;">
                                            <a href="/storage/{{ $i->file }}" target="_blank" class="text-primary">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <i>Belum Ada Data</i>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-6 mt-4">
            <div class="card w-100 shadow" style="height: 500px !important;">
                <div class="card-body">
                    <b>
                        <span
                            style="font-size: 1.2em !important;
                        font-weight: bold !important;
                        fill: rgb(51, 51, 51); !important">
                            DATA PEGAWAI CUTI HARI INI
                        </span>
                    </b>
                    <div class="table-responsive mt-3" style="max-height: 400px; overflow-y: auto;">
                        <table id="myTable" class="table table-bordered table-striped" style="width: 100%;">
                            <thead style="position: sticky; top: 0;" class="bg-info text-white">
                                <tr>
                                    <th width="50%">User</th>
                                    <th>Keterangan</th>
                                    <th width="5px">File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cuti as $i)
                                    <tr>
                                        <td style="vertical-align: top !important;">
                                            <b>Nama: </b><br>
                                            {{ $i->name }}<br><br>

                                            <b>NIP: </b><br>
                                            {{ $i->nip }} <br><br>

                                            <b>Unit Kerja: </b><br>
                                            {{ $i->satuan_kerja }}<br><br>
                                        </td>
                                        <td style="vertical-align: top !important;">
                                            <b>Jenis: </b><br>
                                            {{ $i->jenis }}<br><br>

                                            <b>Keterangan: </b><br>
                                            {{ $i->keterangan }}<br><br>
                                        </td>
                                        <td style="vertical-align: top !important;">
                                            <a href="/storage/{{ $i->file }}" target="_blank" class="text-primary">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <i>Belum Ada Data</i>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        let today = new Date();
        let tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        // Format tanggal misal: 13 Desember 2025
        let options = {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        };
        let todayStr = today.toLocaleDateString('id-ID', options);
        let tomorrowStr = tomorrow.toLocaleDateString('id-ID', options);

        document.addEventListener('DOMContentLoaded', function() {

            fetch('/grafik-absensi')
                .then(r => r.json())
                .then(res => {

                    let dataPoints = res.data.categories;
                    let labels = res.data.values;

                    Highcharts.chart('grafikAbsen', {
                        chart: {
                            type: 'line'
                        },
                        title: {
                            text: 'DISTRIBUSI JAM SCAN PEGAWAI',
                        },
                        subtitle: {
                            text: todayStr + ' - ' + tomorrowStr
                        },
                        xAxis: {
                            categories: dataPoints, // contoh: ["07:00", "16:00"]
                            title: {
                                text: 'Jam'
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Jumlah Scan'
                            },
                            allowDecimals: false
                        },
                        tooltip: {
                            pointFormat: 'Total Scan: <b>{point.y}</b>'
                        },
                        series: [{
                            name: 'Scan Pegawai',
                            data: labels
                        }]
                    });

                });

            fetch('/grafik-izin-cuti')
                .then(r => r.json())
                .then(res => {

                    let categories = res.data.categories;
                    let values = res.data.values;

                    Highcharts.chart('grafikIzinCuti', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'DISTRIBUSI CUTI DAN IZIN HARI INI'
                        },
                        subtitle: {
                            text: 'Tanggal: ' + todayStr
                        },
                        xAxis: {
                            categories: categories,
                            crosshair: true,
                            labels: {
                                style: {
                                    fontSize: '11px'
                                }
                            }
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Jumlah Pengajuan'
                            },
                            allowDecimals: false
                        },
                        tooltip: {
                            pointFormat: '<b>{point.y} Pegawai</b>'
                        },
                        series: [{
                            name: 'Jenis Izin / Cuti',
                            data: values,
                        }]
                    });
                })
                .catch(err => console.error(err));

        });
    </script>
@endpush
