@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <h3 class="font-weight-bold">Data Absensi</h3>
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

                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Foto</th>
                                    <th>User</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Datetime</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formAbsensi">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Absensi</h5>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id" id="id">

                        <div class="text-center">
                            <video style="border: 1px solid grey; border-radius: 8px;" id="camera" width="100%"
                                height="260" autoplay></video>
                            {{-- <canvas style="border: 1px solid grey; border-radius: 8px;" id="canvas" width="100%" height="260" class="d-none"></canvas> --}}
                            <canvas id="canvas" width="350" height="260" class="d-none"></canvas>

                            <br>
                            <button type="button" class="btn btn-warning mt-2" id="btnCapture">
                                Ambil Foto
                            </button>

                            <input type="hidden" id="foto" name="foto">
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Latitude</label>
                            <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                        </div>

                        <div class="form-group">
                            <label>Longitude</label>
                            <input type="text" id="longitude" name="longitude" class="form-control" readonly>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary" id="btnSubmit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/backend/absensi/index.js') }}"></script>
@endpush
