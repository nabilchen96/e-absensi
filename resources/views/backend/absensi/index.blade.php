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

                    <div class="alert alert-primary" style="border-color: #b8daff; background-color: #cce5ff !important;">
                        Update lokasi kerja anda agar sesuai dengan jarak lokasi kerja saat ini. Update lokasi kerja di
                        halaman
                        <a href="{{ url('/detail-user') }}?id={{ Auth::id() }}">Profil</a>
                    </div>
                    <div class="input-group mb-3">
                        <select class="custom-select" id="filterShift">
                            <option value="">Pilih Shift</option>
                            <option>Semua</option>
                            <option value="Reguler">Shift Reguler</option>
                            <option value="Sore">Shift Sore</option>
                            <option value="Malam">Shift Malam</option>
                        </select>
                        <select class="custom-select" id="filterStatus">
                            <option selected>Pilih Status Absensi</option>
                            <option>Semua</option>
                            <option>Diterima</option>
                            <option>Ditolak</option>
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>No</th>
                                    <th>User</th>
                                    <th>Data Absensi</th>
                                    <th>Status Absensi</th>
                                    <th>Status</th>
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

                        <div id="respon_error" class="text-danger"></div>
                        <input type="hidden" name="id" id="id">

                        <div class="form-group">
                            <label>Jenis Absensi</label>
                            <select class="form-control" name="jenis_absensi" id="jenis_absensi" required>
                                <option value="">Pilih Jenis Absensi</option>
                                <option value="Masuk">Absen Masuk</option>
                                <option value="Pulang">Absen Pulang</option>
                            </select>
                        </div>

                        <div id="form_absensi_detail" style="display:none;">
                            <div class="text-center">


                                <video style="border: 1px solid grey; border-radius: 8px;" id="camera" width="100%"
                                    height="260" autoplay></video>
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
                                        <input type="text" id="latitude" name="latitude" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Longitude</label>
                                        <input type="text" id="longitude" name="longitude" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-2">
                                <label>Lokasi Kerja</label>
                                <select required class="form-control" name="id_lokasi_kerja" id="id_lokasi_kerja">
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
                                        <option data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}"
                                            value="{{ $item->id }}">{{ $item->lokasi_kerja }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger" style="font-size: 12px;">
                                    *Update lokasi kerja anda agar sesuai dengan jarak lokasi kerja saat ini. Update lokasi
                                    kerja di
                                    halaman
                                    <a href="{{ url('/detail-user') }}?id={{ Auth::id() }}">Profil</a>
                                </span>
                            </div>

                            <div class="form-group mt-2">
                                <label>Jarak Dari Kantor (Meter)</label>
                                <input type="text" id="jarak" name="jarak" readonly class="form-control"
                                    placeholder="Jarak (meter)" required>
                                <span class="text-info" style="font-size: 12px;" id="notifikasi_jarak"></span>
                            </div>

                            <iframe
                                style="margin-bottom: 10px; border: 1px solid grey; border-radius: 8px; height: 250px; width: 100%;"
                                src="https://www.google.com/maps?q=-3.4391476682335727,102.19011149551001&hl=id&z=13&output=embed"
                                allowfullscreen="" loading="lazy">
                            </iframe>

                            <div id="field_non_reguler" class="border rounded p-3 mt-3"
                                style="display:none;background:#fff8e1;">

                                <div class="form-group">
                                    <label>Upload Bukti</label>
                                    <input type="file" class="form-control" id="bukti" name="bukti"
                                        accept="image/*,.pdf">
                                </div>

                                <div class="form-group mt-2">
                                    <label>Alasan</label>
                                    <textarea placeholder="Alasan" class="form-control" id="alasan" name="alasan" rows="3"></textarea>
                                </div>
                                {{-- <div id="info_non_reguler" class="alert alert-warning mt-2" style="display:none;">
                                </div> --}}

                                <small class="text-danger">
                                    Wajib diisi apabila absensi dilakukan di luar jam reguler atau di luar area kantor.
                                </small>
                                <input type="hidden" name="status_shift" id="status_shift">
                                <input type="hidden" name="status_lokasi" id="status_lokasi">
                            </div>
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary" id="tombol_kirim">Absen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        window.APP_DATA = {
            lokasiKantor: @json($data),
            role: @json(Auth::user()->role)
        };
    </script>
    <script src="{{ asset('js/backend/absensi/index.js') }}"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endpush
