@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Pengajuan Schedule</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    <button data-toggle="modal" data-target="#modal"
                        class="btn btn-primary btn-ms mb-4 d-none d-md-inline-block">
                        Pengajuan
                    </button>

                    <button type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal" data-target="#modalCari">
                        <i class="bi bi-search"></i> Cari
                    </button>

                    {{-- informasi pencarian dan tombol reset form input #modalCari dan mereload ulang data  --}}
                    {{-- informasi pencarian --}}
                    {{-- <div id="infoFilter" class="d-none text-danger" style="font-size: 12px;">
                        Pencarian Data: <span id="textFilter"></span>
                        <a href="#" id="btnResetFilter"> | <i class="bi bi-arrow-repeat"></i> Reset</a>
                    </div> --}}

                    <button type="button" data-toggle="modal" data-target="#modal" class="fab-add d-md-none">
                        <i class="bi bi-plus-lg"></i>
                    </button>

                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama</th>
                                    <th>Tgl Jadwal</th>
                                    <th>Status</th>
                                    <th>Tgl Pengajuan</th>
                                    <th>File</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- ============= -->
    <!--   MODAL FORM  -->
    <!-- ============= -->
    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header p-3">
                        <h5 class="modal-title m-2">Form Pengajuan Schedule</h5>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="id">

                        <ul id="respon_error" class="text-danger mb-4"></ul>

                        <div class="form-group">
                            <label>User <sup class="text-danger">*</sup></label>
                            <select name="id_user" id="id_user" class="" required>
                                <option value="">-- Pilih User --</option>
                                @php
                                    $users = DB::table('users')->where('role', 'Pegawai');
                                    if (Auth::user()->role == 'Admin') {
                                        $users = $users->get();
                                    }

                                    // 🏢 ROLE OPD → pegawai dalam unit kerja yang sama
                                    elseif (Auth::user()->role == 'OPD') {
                                        $idUnitKerja = Auth::user()->id_unit_kerja_pandu; //107

                                        $users = $users
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
                                        $users = $users->where('id', Auth::id())->get();
                                    }
                                @endphp
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>File<sup class="text-danger">*</sup></label>
                            <input type="file" name="file" id="file" class="form-control form-control-sm"
                                required>
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
                    <div class="modal-header p-3">
                        <h5 class="modal-title m-2">Form Pengajuan Schedule</h5>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="id">

                        <ul id="respon_error" class="text-danger mb-4"></ul>

                        <div class="form-group">
                            <label>Status Pengajuan<sup class="text-danger">*</sup></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="">-- Pilih Status --</option>
                                <option>Belum Diajukan</option>
                                <option>Pengajuan</option>
                                @if(Auth::user()->role != 'Pegawai')
                                    <option>Disetujui</option>
                                    <option>Ditolak</option>
                                @endif
                            </select>
                        </div>
                        @if(Auth::user()->role != 'Pegawai')
                            <div class="form-group">
                                <label>Catatan <sup class="text-danger">*</sup></label>
                                <textarea name="catatan" id="catatan" cols="30" rows="10" class="form-control"
                                placeholder="Catatan"></textarea>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer p-3">
                        <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                        <button id="tombol_kirim" class="btn btn-primary btn-sm">Submit</button>
                    </div>

                </form>
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
                        <label>User <sup class="text-danger">*</sup></label>
                        <select id="idUserSearch" class="">
                            <option value="">-- Semua User --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="form-group">
                        <label>Tanggal Dari <sup class="text-danger">*</sup></label>
                        <input type="date" id="tanggalDari" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Ke <sup class="text-danger">*</sup></label>
                        <input type="date" id="tanggalSampai" class="form-control">
                    </div> --}}
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
    <script>
        window.APP_DATA = {
            role: @json(Auth::user()->role)
        };
    </script>
    <script src="{{ asset('js/backend/schedule_request/index.js') }}"></script>
@endpush
