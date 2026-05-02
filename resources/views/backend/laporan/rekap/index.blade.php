@extends('backend.app')
@section('content')
    <style>

    </style>
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Laporan Rekap</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    <button type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal" data-target="#modalCari">
                        Buat Laporan
                    </button>

                    <div class="d-flex gap-2 align-items-start mb-3">
                        <select class="flex-grow-1" placeholder="Cari OPD" id="id_opd_pandu" name="id_opd_pandu" required>
                            @php
                                $lokasi = DB::table('lokasi_kerjas')->select('lokasi_kerjas.*');

                                if (Auth::user()->role == 'Admin') {
                                    $lokasi = $lokasi->get();
                                } elseif (Auth::user()->role == 'OPD') {
                                    $lokasi = $lokasi->where('id_pandu', Auth::user()->id_unit_kerja_pandu)->get();
                                } elseif (Auth::user()->role == 'SKPD') {
                                    $idSkpd = Auth::user()->id_skpd_pandu;

                                    $lokasi = $lokasi
                                        ->whereIn('lokasi_kerjas.id_pandu', function ($q) use ($idSkpd) {
                                            $q->select('id_unit_kerja_pandu')
                                                ->from('users')
                                                ->where('id_skpd_pandu', $idSkpd)
                                                ->whereNotNull('id_unit_kerja_pandu');
                                        })
                                        ->get();
                                }
                            @endphp
                            @foreach ($lokasi as $l)
                                <option value="{{ $l->id_pandu }}">
                                    {{ $l->lokasi_kerja }}
                                </option>
                            @endforeach
                        </select>

                        <button style="height: 38px;" class="input-group-text" id="btnCari">
                            <i class="bi bi-search"></i> &nbsp; Cari
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th width="5%"></th>
                                    <th>OPD</th>
                                    <th>Judul Laporan</th>
                                    <th>Tanggal Rekap</th>
                                    <th>Status</th>
                                    <th>Tanggal Buat</th>
                                    <th width="5%"></th>
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
                    <h5 class="modal-title m-2">Form Buat Laporan</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Unit Kerja <sup class="text-danger">*</sup></label>
                        <select id="id_unit_kerja_pandu" name="id_unit_kerja_pandu" required>
                            @php
                                $lokasi = DB::table('lokasi_kerjas')->select('lokasi_kerjas.*');

                                if (Auth::user()->role == 'Admin') {
                                    $lokasi = $lokasi->get();
                                } elseif (Auth::user()->role == 'OPD') {
                                    $lokasi = $lokasi->where('id_pandu', Auth::user()->id_unit_kerja_pandu)->get();
                                } elseif (Auth::user()->role == 'SKPD') {
                                    $idSkpd = Auth::user()->id_skpd_pandu;

                                    $lokasi = $lokasi
                                        ->whereIn('lokasi_kerjas.id_pandu', function ($q) use ($idSkpd) {
                                            $q->select('id_unit_kerja_pandu')
                                                ->from('users')
                                                ->where('id_skpd_pandu', $idSkpd)
                                                ->whereNotNull('id_unit_kerja_pandu');
                                        })
                                        ->get();
                                }
                            @endphp
                            @foreach ($lokasi as $l)
                                <option value="{{ $l->id_pandu }}">
                                    {{ $l->lokasi_kerja }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Laporan <sup class="text-danger">*</sup></label>
                        <input type="text" id="judul_laporan" name="judul_laporan" required placeholder="Judul Laporan"
                            class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Dari <sup class="text-danger">*</sup></label>
                        <input type="date" id="tanggal_awal" name="tanggal_awal" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Ke <sup class="text-danger">*</sup></label>
                        <input type="date" id="tanggal_akhir" name="tanggal_akhir" required class="form-control">
                    </div>
                </div>
                <div class="modal-footer p-3">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                    <button id="btnSubmit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        id_unit_kerja_pandu = new TomSelect('#id_unit_kerja_pandu');
        id_opd_pandu = new TomSelect('#id_opd_pandu');
    </script>
    <script src="{{ asset('skydash/js/axios/this/axios.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById('btnSubmit').addEventListener('click', function() {

            let id_unit_kerja_pandu = document.getElementById('id_unit_kerja_pandu').value;
            let judul_laporan = document.getElementById('judul_laporan').value;
            let tanggal_awal = document.getElementById('tanggal_awal').value;
            let tanggal_akhir = document.getElementById('tanggal_akhir').value;


            // validasi sederhana
            if (!id_unit_kerja_pandu || !judul_laporan || !tanggal_awal || !tanggal_akhir) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Semua field wajib diisi!'
                });
                return;
            }

            Swal.fire({
                title: 'Proses generate laporan?',
                text: "Data akan diproses di background",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {

                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Harap tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    axios.post('/generate-laporan-rekap', {
                            id_unit_kerja_pandu: id_unit_kerja_pandu,
                            judul_laporan: judul_laporan,
                            tanggal_awal: tanggal_awal,
                            tanggal_akhir: tanggal_akhir
                        })
                        .then(function(response) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Laporan sedang diproses di background'
                            });

                            $('#modalCari').modal('hide');

                        })
                        .catch(function(error) {

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan'
                            });

                        });

                }

            });

        });
    </script>
    <script src="{{ asset('js/backend/laporan/rekap/index.js') }}"></script>
@endpush
