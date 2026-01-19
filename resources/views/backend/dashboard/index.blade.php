@extends('backend.app')
@section('content')
    <style>
        .jam-kerja-card {
            /* background-color: #3f51b5; */
            /* biru */
            color: #fff;
            border-radius: 10px;
            padding: 20px;
        }

        .jam-kerja-card .divider {
            width: 1px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .jam-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .jam-value {
            font-size: 22px;
            font-weight: bold;
        }

        .jam-title {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 8px;
        }

        .profile-card {
            background: #fff;
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .profile-icon {
            width: 100%;
            aspect-ratio: 1/1;
            background-color: #e60000;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 56px;
        }

        .profile-name {
            font-weight: 700;
            font-size: 14px;
        }

        .profile-text {
            font-size: 10px;
            line-height: 1.5;
        }

        .menu-card {
            height: 65px;
        }

        .menu-text {
            font-size: 12px;
            line-height: 1;
            text-align: center;
        }
    </style>
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Dashboard</h3>
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->role != 'Pegawai')
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
                            <table class="table-bordered table table-striped" style="width: 100%;">
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
                            <table class="table table-bordered table-striped" style="width: 100%;">
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
                                                <a href="/storage/{{ $i->file }}" target="_blank"
                                                    class="text-primary">
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
    @else
        <div class="row">
            <div class="col-lg-6 d-lg-none mt-4">
                <div class="profile-card">
                    <div class="row no-gutters align-items-center">

                        <!-- COL GAMBAR -->
                        <div class="col-6 pr-2">
                            <div class="profile-icon">
                                <i class="bi bi-person-circle"></i>
                            </div>
                        </div>

                        <!-- COL KETERANGAN -->
                        <div class="col-6 pl-2">
                            <div class="profile-name">
                                {{ Auth::user()->name }}
                                <hr>
                            </div>
                            <div class="profile-text">
                                @php
                                    @$profil = DB::table('detail_users')->where('user_id', Auth::id())->first();
                                    @$lokasi = DB::table('lokasi_kerjas')->where('id', $profil->satuan_kerja)->first();
                                @endphp
                                <b>NIP.</b> {{ $profil->nip }}<br>
                                <b>Jabatan.</b> {{ $profil->jabatan }}<br>
                                <b>Status.</b> {{ $profil->jenis_asn }}<br>
                                <b>Satuan Kerja.</b> {{ @$lokasi->lokasi_kerja }}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="button" style="border-radius: 8px !important;" class="btn btn-info btn-sm mb-4 btn-block"
                    data-toggle="modal" data-target="#modal">
                    <i class="bi bi-calendar3"></i>&nbsp; Absen Sekarang
                </button>
                <div class="modal fade" id="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="formAbsensi">
                                <div class="modal-header">
                                    <h5 class="modal-title">Form Absensi</h5>
                                </div>

                                <div class="modal-body">

                                    <div id="respon_error" class="text-danger"></div>
                                    <input type="hidden" name="id" id="id">
                                    <div class="text-center">


                                        <video style="border: 1px solid grey; border-radius: 8px;" id="camera"
                                            width="100%" height="260" autoplay></video>
                                        {{-- <canvas style="border: 1px solid grey; border-radius: 8px;" id="canvas" width="100%" height="260" class="d-none"></canvas> --}}
                                        <canvas id="canvas" width="350" height="260" class="d-none"></canvas>

                                        <br>
                                        <button style="border-radius: 8px !important;" type="button"
                                            class="btn-sm btn-block btn btn-warning mt-2" id="btnCapture">
                                            Ambil Foto
                                        </button>

                                        <input type="hidden" id="foto" name="foto">

                                        <img id="previewFoto" src="" alt="Preview Foto"
                                            style="border:1px solid grey; border-radius:8px; margin-top:10px; width:100%; height:auto; display:none;">

                                    </div>

                                    <hr>

                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Latitude</label>
                                                <input type="text" id="latitude" name="latitude"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Longitude</label>
                                                <input type="text" id="longitude" name="longitude"
                                                    class="form-control" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>Lokasi Kerja</label>
                                        <select required class="form-control" name="id_lokasi_kerja"
                                            id="id_lokasi_kerja">
                                            <option value="">Pilih Lokasi Kerja</option>
                                            @php
                                                @$lk = DB::table('lokasi_kerja_users')
                                                    ->leftjoin(
                                                        'lokasi_kerjas',
                                                        'lokasi_kerjas.id',
                                                        '=',
                                                        'lokasi_kerja_users.id_lokasi_kerja',
                                                    )
                                                    ->select('lokasi_kerjas.*')
                                                    ->where('lokasi_kerja_users.id_user', Auth::id())
                                                    ->get();
                                            @endphp
                                            @foreach (@$lk as $item)
                                                <option data-lat="{{ $item->latitude }}"
                                                    data-lng="{{ $item->longitude }}" value="{{ $item->id }}">
                                                    {{ $item->lokasi_kerja }}</option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" style="font-size: 12px;">
                                            *Update lokasi kerja anda agar sesuai dengan jarak lokasi kerja saat ini. Update
                                            lokasi
                                            kerja di
                                            halaman
                                            <a href="{{ url('/detail-user') }}?id={{ Auth::id() }}">Profil</a>
                                        </span>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label>Jarak Dari Kantor (Meter)</label>
                                        <input type="text" id="jarak" name="jarak" readonly
                                            class="form-control" placeholder="Jarak (meter)" required>
                                        <span class="text-info" style="font-size: 12px;" id="notifikasi_jarak"></span>
                                    </div>

                                    <iframe
                                        style="margin-bottom: 10px; border: 1px solid grey; border-radius: 8px; height: 250px; width: 100%;"
                                        src="https://www.google.com/maps?q=-3.4391476682335727,102.19011149551001&hl=id&z=13&output=embed"
                                        allowfullscreen="" loading="lazy">
                                    </iframe>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                                    <button class="btn btn-primary" id="tombol_kirim">Absen</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4">
                <div class="font-weight-bold text-warning mb-2">
                    JAM KERJA HARI INI <br>
                    {{ now() }}
                </div>
                <div class="jam-kerja-card bg-primary">
                    <div class="bg-primary d-flex align-items-center justify-content-between text-center">
                        @php
                            $shift = DB::table('schedules')
                                ->leftjoin('shifts', 'shifts.id', '=', 'schedules.id_shift')
                                ->where('schedules.tanggal', date('Y-m-d'))
                                ->first();

                            // dd($shift);

                        @endphp
                        <div class="flex-fill">
                            <div class="jam-label">Masuk</div>
                            <div class="jam-value">{{ @$shift->jam_masuk ?? '-' }}</div>
                        </div>

                        <div class="divider mx-3" style="height:50px;"></div>

                        <div class="flex-fill">
                            <div class="jam-label">Pulang</div>
                            <div class="jam-value">{{ @$shift->jam_pulang ?? '-' }}</div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="font-weight-bold text-warning">MENU AKSES</div>
            </div>
            <a href="{{ url('list-absensi') }}" class="col-4 mt-2">
                <div class="menu-card bg-primary d-flex flex-column justify-content-center align-items-center text-white"
                    style="width: 100%; height: 65px; border-radius: 8px;">
                    <i style="font-size: 1rem;" class="bi bi-clock fs-2"></i>
                    <span class="menu-text">Riwayat <br> Absensi</span>
                </div>
            </a>

            <a href="perizinan" class="col-4 mt-2">
                <div class="bg-primary d-flex flex-column justify-content-center align-items-center text-white"
                    style="width: 100%; height: 65px; border-radius: 8px;">
                    <i style="font-size: 1rem;" class="bi bi-file-earmark-text fs-2"></i>
                    <span class="menu-text">Izin</span>
                </div>
            </a>
            <a href="{{ url('cuti') }}" class="col-4 mt-2">
                <div class="bg-primary d-flex flex-column justify-content-center align-items-center text-white"
                    style="width: 100%; height: 65px; border-radius: 8px;">
                    <i style="font-size: 1rem;" class="bi bi-file-earmark-text fs-2"></i>
                    <span class="menu-text">Cuti</span>
                </div>
            </a>
            <a href="#" class="col-4 mt-4">
                <div class="bg-primary d-flex flex-column justify-content-center align-items-center text-white"
                    style="width: 100%; height: 65px; border-radius: 8px;">
                    <i style="font-size: 1rem;" class="bi bi-file-earmark-text fs-2"></i>
                    <span class="menu-text">LKH</span>
                </div>
            </a>
            <a href="{{ url('laporan-shift') }}" class="col-4 mt-4">
                <div class="bg-primary d-flex flex-column justify-content-center align-items-center text-white"
                    style="width: 100%; height: 65px; border-radius: 8px;">
                    <i style="font-size: 1rem;" class="bi bi-bar-chart fs-2"></i>
                    <span class="menu-text">Rekap</span>
                </div>
            </a>
            <a href="{{ url('detail-user') }}?id={{ Auth::id() }}" class="col-4 mt-4">
                <div class="bg-primary d-flex flex-column justify-content-center align-items-center text-white"
                    style="width: 100%; height: 65px; border-radius: 8px;">
                    <i style="font-size: 1rem;" class="bi bi-person-circle fs-2"></i>
                    <span class="menu-text">Profile</span>
                </div>
            </a>

            <div class="col-12 mt-4">
                <div class="font-weight-bold text-warning mb-2">
                    INFORMASI TERKINI
                </div>
                <ul class="list-group">
                    @php
                        $pengumuman = DB::table('pengumumen')->limit(10)->get();
                    @endphp
                    @forelse ($pengumuman as $item)
                        <li class="list-group-item">
                            {{ $item->pengumuman }}
                            @if ($item->file)
                                <a href="{{ asset('storage/' . $item->file) }}">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>
                            @else
                                <i class="bi bi-file-earmark-text"></i>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item">
                            Belum ada pengumuman di tampilkan
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif
@endsection
@push('script')
    <script>
        window.APP_DATA = {
            lokasiKantor: @json($data)
        };
    </script>
    <script src="{{ asset('js/backend/absensi/index.js') }}"></script>
    @if (Auth::user()->role != 'Pegawai')
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
    @else
    @endif
@endpush
