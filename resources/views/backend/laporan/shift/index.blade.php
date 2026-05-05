@extends('backend.app')
@section('content')
    <style>
        @media (max-width: 768px) {

            /* .card,
        .card-body {
            background: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            border: none !important;
        } */

            .card:not(.keep-style),
            .card:not(.keep-style) .card-body:not(.keep-style) {
                background: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                border: none !important;
            }
        }
    </style>
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Laporan Shift</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    <button type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal" data-target="#modalCari">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <div class="row">

                        <div class="col-lg-3 mb-4">
                            <div class="card bg-gradient-success card-img-holder keep-style text-white">
                                <div class="card-body keep-style">
                                    <img src="https://themewagon.github.io/purple-react/static/media/circle.953c9ca0.svg"
                                        class="card-img-absolute" alt="circle">
                                    <h4 class="font-weight-normal mb-3">
                                        Total Durasi
                                        <i class="bi bi-person-circle float-right"></i>
                                    </h4>
                                    <h2 id="totalJamKerja">00:00:00</h2>
                                    <span>Jam Kerja</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-4">
                            <div class="card bg-gradient-primary card-img-holder keep-style text-white">
                                <div class="card-body keep-style">
                                    <img src="https://themewagon.github.io/purple-react/static/media/circle.953c9ca0.svg"
                                        class="card-img-absolute" alt="circle">
                                    <h4 class="font-weight-normal mb-3">
                                        Total Durasi
                                        <i class="bi bi-person-circle float-right"></i>
                                    </h4>
                                    <h2 id="totalTerlambat">00:00:00</h2>
                                    <span>Terlambat</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-4">
                            <div class="card bg-gradient-info card-img-holder keep-style text-white">
                                <div class="card-body keep-style">
                                    <img src="https://themewagon.github.io/purple-react/static/media/circle.953c9ca0.svg"
                                        class="card-img-absolute" alt="circle">
                                    <h4 class="font-weight-normal mb-3">
                                        Total Absen
                                        <i class="bi bi-person-circle float-right"></i>
                                    </h4>
                                    <h2 id="totalHadir">0</h2>
                                    <span>Datang dan Pulang</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card bg-gradient-danger card-img-holder keep-style text-white">
                                <div class="card-body keep-style">
                                    <img src="https://themewagon.github.io/purple-react/static/media/circle.953c9ca0.svg"
                                        class="card-img-absolute" alt="circle">
                                    <h4 class="font-weight-normal mb-3">
                                        Total Jadwal
                                        <i class="bi bi-person-circle float-right"></i>
                                    </h4>
                                    <h2 id="totalShift">0</h2>
                                    <span>Shift</span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>User</th>
                                    <th>Shift</th>
                                    <th>Scan Masuk</th>
                                    <th>Scan Pulang</th>
                                    <th>Terlambat</th>
                                    {{-- <th>Pulang Cepat</th> --}}
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCari" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header p-3">
                    <h5 class="modal-title m-2">Form Cari</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>User</label>
                        <select id="idUserSearch" class="">
                            @php
                                $users = DB::table('users')
                                    ->leftjoin('detail_users', 'detail_users.user_id', '=', 'users.id')
                                    ->select('users.*', 'detail_users.nip');

                                if (Auth::user()->role == 'Admin') {
                                    $users = $users->get();

                                    // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
                                } elseif (Auth::user()->role == 'OPD') {
                                    $idUnitKerja = Auth::user()->id_unit_kerja_pandu; //107

                                    $users = $users
                                        ->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
                                        // ->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_lokasi_kerja', '=', 'lokasi_kerjas.id')
                                        ->leftjoin(
                                            'lokasi_kerjas',
                                            'lokasi_kerjas.id',
                                            '=',
                                            'lokasi_kerja_users.id_lokasi_kerja',
                                        )
                                        ->where('lokasi_kerjas.id_pandu', $idUnitKerja)
                                        ->get();
                                } elseif (Auth::user()->role == 'SKPD') {
                                    $idSkpd = Auth::user()->id_skpd_pandu;

                                    $users = $users
                                        ->leftJoin('lokasi_kerja_users', 'lokasi_kerja_users.id_user', '=', 'users.id')
                                        ->leftJoin(
                                            'lokasi_kerjas',
                                            'lokasi_kerjas.id',
                                            '=',
                                            'lokasi_kerja_users.id_lokasi_kerja',
                                        )
                                        ->whereIn('lokasi_kerjas.id_pandu', function ($q) use ($idSkpd) {
                                            $q->select('id_unit_kerja_pandu')
                                                ->from('users')
                                                ->where('id_skpd_pandu', $idSkpd)
                                                ->whereNotNull('id_unit_kerja_pandu');
                                        })
                                        ->get();
                                } elseif (Auth::user()->role == 'Pegawai') {
                                    $users = $users->where('users.id', Auth::id())->get();
                                }
                            @endphp
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} [ NIP: {{ @$user->nip }} ]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Dari</label>
                        <input type="date" id="tanggalDari" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Ke</label>
                        <input type="date" id="tanggalSampai" class="form-control">
                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    <button id="btnCari" class="btn btn-primary btn-sm">Cari</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/backend/laporan/shift/index.js') }}"></script>
@endpush
