@extends('backend.app')
@section('content')
    <style>
        @media (max-width: 768px) {

            /* SCOPED — hanya bekerja untuk halaman dengan #absensi-container */
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

            #absensi-container .aksi-btn i {
                font-size: 1.1rem !important;
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
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Lokasi Kerja</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Lokasi Kerja ...">
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
                                    <th>Lokasi</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
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
    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form">
                    <div class="modal-header p-3">
                        <h5 class="modal-title m-2">Form Shift</h5>
                    </div>

                    <div class="modal-body">
                        <ul id="respon_error" class="text-danger mb-4"></ul>
                        <input type="hidden" name="id" id="id">

                        <div class="form-group">
                            <label>Lokasi Kerja <sup class="text-danger">*</sup></label>
                            <input type="text" name="lokasi_kerja" id="lokasi_kerja" class="form-control form-control-sm"
                            required placeholder="Lokasi Kerja">
                        </div>

                        <div class="form-group">
                            <label>Longitude <sup class="text-danger">*</sup></label>
                            <input type="text" name="longitude" id="longitude" class="form-control form-control-sm"
                                required placeholder="Longitude">
                        </div>

                        <div class="form-group">
                            <label>Latitude <sup class="text-danger">*</sup></label>
                            <input type="text" name="latitude" id="latitude" class="form-control form-control-sm"
                                required placeholder="Latitude">
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
    <script src="{{ asset('js/backend/lokasi_kerja/index.js') }}"></script>
@endpush
