@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Pegawai</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    @if (Auth::user()->role == 'Admin')
                        <button id="btnSync" class="btn btn-primary btn-md mb-4 d-none d-md-inline-block">Sinkron
                            User</button>
                    @endif


                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari User ..."
                            aria-describedby="basic-addon2">
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
                                    <th width="5%">No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Lokasi Kerja</th>
                                    <th>Role</th>
                                    <th>Created At</th>
                                    <th width="5%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="{{ asset('js/backend/pegawai/index.js') }}"></script>
@endpush
