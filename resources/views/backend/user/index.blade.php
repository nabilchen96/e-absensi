@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data User</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    <button id="btnSyncBukanPegawai" class="btn btn-primary btn-md mb-4 d-none d-md-inline-block">Sinkron User</button>


                    <button type="button" class="btn btn-primary btn-md mb-4 d-none d-md-inline-block" data-toggle="modal"
                        data-target="#modal">
                        Tambah
                    </button>
                    
                    <!-- Floating Button Mobile -->
                    <button type="button" class="fab-add d-md-none" data-toggle="modal" data-target="#modal">
                        <i class="bi bi-plus-lg"></i>
                    </button>

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

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header p-3">
                        <h5 class="modal-title m-2" id="exampleModalLabel">User Form</h5>
                    </div>
                    <div class="modal-body">
                        <ul id="respon_error" class="text-danger mb-4"></ul>
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address <sup class="text-danger">*</sup></label>
                            <input name="email" id="email" type="email" placeholder="email"
                                class="form-control form-control-sm" aria-describedby="emailHelp" required>
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap <sup class="text-danger">*</sup></label>
                            <input name="name" id="name" type="text" placeholder="Nama Lengkap"
                                class="form-control form-control-sm" aria-describedby="emailHelp" required>
                        </div>
                        <div class="form-group">
                            <label>Password <sup class="text-danger">*</sup></label>
                            <input name="password" id="password" type="password" placeholder="Password"
                                class="form-control form-control-sm">
                            <span class="text-danger error" style="font-size: 12px;" id="password_alert"></span>
                        </div>
                        <div class="form-group">
                            <label>Role <sup class="text-danger">*</sup></label>
                            <select name="role" class="form-control" id="role" required>
                                <option value="">PILIH ROLE</option>
                                <option>Admin</option>
                            </select>
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
    <script src="{{ asset('js/backend/user/index.js') }}"></script>
@endpush
