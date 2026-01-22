@extends('backend.app')
@section('content')
    <style>
        @media (max-width: 768px) {

            #absensi-container .card,
            #absensi-container .card-body {
                background: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                border: none !important;
            }

            #absensi-container .table-striped>tbody>tr {
                background-color: #ffffff !important;
            }

            #absensi-container .table-striped>tbody>tr:nth-of-type(odd) {
                background-color: #ffffff !important;
            }

            #absensi-container table td:first-child {
                display: none !important;
            }

            #absensi-container table thead {
                display: none;
            }

            #absensi-container table.dataTable.dtr-inline.collapsed>tbody>tr>td.child {
                border: none !important;
                padding: 5px 10px !important;
                background: transparent !important;
            }

            #absensi-container table.dataTable td {
                border: none !important;
            }

            #absensi-container table.dataTable tbody td {
                padding: 4px 10px !important;
            }

            #absensi-container table tbody tr {
                display: block;
                margin-bottom: 15px;
                border: 1px solid #ddd;
                padding: 10px;
                border-radius: 10px;
                background: #f8f9fa !important;
            }

            #absensi-container table tbody td {
                display: block !important;
                padding: 5px 10px !important;
            }

            #absensi-container table tbody td:before {
                content: attr(data-label);
                font-weight: bold;
            }

            #absensi-container table tbody td:last-child:before,
            #absensi-container table tbody td:nth-last-child(2):before {
                content: "" !important;
            }

            #absensi-container .fab-add {
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 55px;
                height: 55px;
                border-radius: 50%;
                background-color: #0d6efd;
                color: white;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
                z-index: 999;
            }
        }
    </style>

    {{-- <nav class="navbar navbar-light bg-white navbar-expand d-md-none fixed-bottom p-0">
        <ul class="navbar-nav nav-justified w-100">
            <li class="nav-item">
                <a href="{{ url('dashboard') }}" class="nav-link">
                    <i class="icon-grid menu-icon"></i><br>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="#" data-toggle="modal" data-target="#modal" class="nav-link">
                    <i class="bi bi-file-earmark-text"></i><br>
                    Form Cuti
                </a>
            </li>
        </ul>
    </nav> --}}

    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Cuti</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="absensi-container">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    <button type="button" class="btn btn-primary btn-md mb-4 d-none d-md-inline-block" data-toggle="modal"
                        data-target="#modal">
                        Tambah
                    </button>

                    <button type="button" class="fab-add d-md-none" data-toggle="modal" data-target="#modal">
                        <i class="bi bi-plus-lg"></i>
                    </button>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Perizinan ...">
                        <div class="input-group-append">
                            <button style="height: 38px;" class="input-group-text" id="btnCari">
                                <i class="bi bi-search"></i> &nbsp; Cari
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th width="25%">Keterangan</th>
                                    <th>File</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- Modal -->
    <!-- ========================= -->
    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="form" enctype="multipart/form-data">
                    @csrf
                    <ul id="respon_error" class="text-danger mb-4"></ul>
                    <input type="hidden" id="id" name="id">

                    <div class="modal-header">
                        <h5 class="modal-title">Form Cuti</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>User <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="user_id" id="user_id" required>
                                <option value="">-- Pilih User --</option>
                                @php
                                    $user = DB::table('users');
                                    if (Auth::user()->role == 'Admin') {
                                        $user = $user->get();
                                    }

                                    // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
                                    elseif (Auth::user()->role == 'OPD') {
                                        $idUnitKerja = Auth::user()->id_unit_kerja_pandu; //107

                                        $user = $user
                                            ->leftjoin(
                                                'lokasi_kerja_users',
                                                'lokasi_kerja_users.id_user',
                                                '=',
                                                'users.id',
                                            )
                                            // ->leftjoin('lokasi_kerja_users', 'lokasi_kerja_users.id_lokasi_kerja', '=', 'lokasi_kerjas.id')
                                            ->leftjoin(
                                                'lokasi_kerjas',
                                                'lokasi_kerjas.id',
                                                '=',
                                                'lokasi_kerja_users.id_lokasi_kerja',
                                            )
                                            ->select('users.*')
                                            ->where('lokasi_kerjas.id_pandu', $idUnitKerja)
                                            ->get();
                                    }

                                    // ROLE PEGAWAI
                                    else {
                                        $user = $user->where('id', Auth::id())->get();
                                    }
                                @endphp
                                @foreach ($user as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Awal <sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control" name="tanggal_awal" id="tanggal_awal"
                                max="{{ date('Y') }}-12-31" required>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Akhir <sup class="text-danger">*</sup></label>
                            <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir"
                                max="{{ date('Y') }}-12-31" required>
                        </div>

                        <div class="form-group">
                            <label>Jenis <sup class="text-danger">*</sup></label>
                            <select class="form-control" name="jenis" id="jenis" required>
                                <option>Cuti Tahunan</option>
                                <option>Cuti Bersalin</option>
                                <option>Cuti Alasan Penting</option>
                                <option>Cuti Sakit</option>
                                <option>Tugas Belajar</option>
                                <option>Cuti Diluar Tanggungan Negara</option>
                                <option>Cuti Besar</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Keterangan <sup class="text-danger">*</sup></label>
                            <textarea class="form-control" name="keterangan" id="keterangan" required placeholder="Keterangan Cuti"></textarea>
                        </div>

                        <div class="form-group">
                            <label>File</label>
                            <input type="file" class="form-control" name="file">
                            <div id="previewFile" class="mt-2"></div>
                        </div>

                    </div>

                    <div class="modal-footer p-3">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        <button id="tombol_kirim" class="btn btn-primary btn-sm">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modalStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form id="formStatus">
                    @csrf
                    <ul id="respon_error" class="text-danger mb-4"></ul>
                    <input type="hidden" id="id" name="id">

                    <div class="modal-header">
                        <h5 class="modal-title">Form Status Perizinan</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Status <sup class="text-danger">*</sup> </label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="">PILIH STATUS</option>
                                <option>Diterima</option>
                                <option>Ditolak</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Ubah Semua <sup class="text-danger">*</sup> </label><br>
                            <input type="checkbox" name="ubah_semua" value="1">
                            centang pilihan ini jika anda ingin merubah semua status dalam satu pengajuan yang sama
                        </div>
                    </div>

                    <div class="modal-footer p-3">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        <button id="tombol_kirim" class="btn btn-primary btn-sm">Submit</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        window.APP_DATA = {
            role: @json(Auth::user()->role)
        };
    </script>
    <script src="{{ asset('js/backend/cuti/index.js') }}"></script>
@endpush
