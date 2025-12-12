@extends('backend.app')
@section('content')
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

                    {{-- informasi pencarian dan tombol reset form input #modalCari dan mereload ulang data  --}}
                    {{-- informasi pencarian --}}
                    {{-- <div id="infoFilter" class="d-none text-danger" style="font-size: 12px;">
                        Pencarian Data: <span id="textFilter"></span>
                        <a href="#" id="btnResetFilter"> | <i class="bi bi-arrow-repeat"></i> Reset</a>
                    </div> --}}

                    <button type="button" class="fab-add d-md-none" data-toggle="modal" data-target="#modal">
                        <i class="bi bi-plus-lg"></i>
                    </button>


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
                                    <th>Pulang Cepat</th>
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
                            <option value="">-- Semua User --</option>
                            @php
                                $users = DB::table('users')->get();
                            @endphp
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
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
