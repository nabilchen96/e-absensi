@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Data Detail User</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home"
                                type="button" role="tab" aria-controls="home" aria-selected="true">Profil</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile"
                                type="button" role="tab" aria-controls="profile" aria-selected="false">Lokasi
                                Kerja</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif


                            <form action="{{ url('store-detail-user') }}" method="POST">
                                @csrf
                                <div class="row">

                                    <input type="hidden" name="id" value="{{ $data->id_user }}">

                                    <!-- Email -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input name="email" id="email" type="email" placeholder="email"
                                                class="form-control form-control-sm" value="{{ $data->email ?? '' }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Nabil -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input name="name" id="name" type="text" placeholder="name"
                                                class="form-control form-control-sm" value="{{ $data->name ?? '' }}"
                                                required>
                                        </div>
                                    </div>

                                    <!-- Jenis Kelamin -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Jenis Kelamin</label>
                                            <select name="jenis_kelamin" class="form-control form-control-sm">
                                                <option value="">-- pilih --</option>
                                                <option value="Laki-laki"
                                                    {{ isset($data) && $data->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="Perempuan"
                                                    {{ isset($data) && $data->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Tempat Lahir -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Tempat Lahir</label>
                                            <input name="tempat_lahir" type="text" placeholder="Tempat lahir"
                                                class="form-control form-control-sm"
                                                value="{{ $data->tempat_lahir ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Tanggal Lahir -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Tanggal Lahir</label>
                                            <input name="tanggal_lahir" type="date" class="form-control form-control-sm"
                                                value="{{ $data->tanggal_lahir ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- NIP -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>NIP</label>
                                            <input name="nip" type="text" placeholder="NIP"
                                                class="form-control form-control-sm" value="{{ $data->nip ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Jenis ASN -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Jenis ASN</label>
                                            <select name="jenis_asn" class="form-control form-control-sm">
                                                <option value="">-- pilih --</option>
                                                <option value="PNS"
                                                    {{ isset($data) && $data->jenis_asn == 'PNS' ? 'selected' : '' }}>PNS
                                                </option>
                                                <option value="PPPK"
                                                    {{ isset($data) && $data->jenis_asn == 'PPPK' ? 'selected' : '' }}>PPPK
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Jabatan -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Jabatan</label>
                                            <input name="jabatan" type="text" placeholder="Jabatan"
                                                class="form-control form-control-sm" value="{{ $data->jabatan ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Instansi Kerja -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Instansi Kerja</label>
                                            <input name="instansi_kerja" type="text" placeholder="Instansi Kerja"
                                                class="form-control form-control-sm"
                                                value="{{ $data->instansi_kerja ?? '' }}">
                                        </div>
                                    </div>

                                    <!-- Satuan Kerja -->
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label>Satuan Kerja</label>

                                            {{-- <select name="satuan_kerja" id="satuan_kerja" required>
                                                <option value="">-- Pilih Lokasi --</option>
                                                @php
                                                    $lokasis = DB::table('lokasi_kerjas')->get();
                                                @endphp
                                                @foreach ($lokasis as $lokasi)
                                                    <option {{ $lokasi->id == $data->satuan_kerja ? 'selected' : '' }}
                                                        value="{{ $lokasi->id }}">{{ $lokasi->lokasi_kerja }}</option>
                                                @endforeach
                                            </select> --}}
                                            <input type="text" class="form-control" value="{{ $data->satuan_kerja }}">
                                            {{-- <span class="text-danger mt-2" style="font-size: 12px;">
                                                *Update satuan kerja agar jarak antar lokasi kerja dan lokasi anda sesuai
                                            </span> --}}
                                        </div>
                                    </div>

                                </div>

                                <!-- Button -->
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                            <button type="button" class="btn btn-primary btn-md mb-4 d-none d-md-inline-block"
                                data-toggle="modal" data-target="#modal">
                                Tambah
                            </button>

                            <table id="myTable" class="table table-striped" style="width: 100%;">
                                <thead class="bg-info text-white">
                                    <tr>
                                        <th>Lokasi</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lokasi as $item)
                                        <tr>
                                            <td style="width: 50%; white-space: unset !important;">{{ $item->lokasi_kerja }}</td>
                                            <td>{{ $item->latitude }}</td>
                                            <td>{{ $item->longitude }}</td>
                                            <td style="width: 5px !important;">
                                                <a href="javascript:void(0)" onclick="hapusData({{$item->id}})">
                                                    <i style="font-size: 1.2rem;" class="text-danger bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('store-lokasi-kerja-user') }}" method="POST">
                    @csrf
                    <div class="modal-header p-3">
                        <h5 class="modal-title m-2">Form Lokasi Kerja</h5>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="id_user" id="id_user" value="{{ $data->id_user }}" required>

                        <div class="form-group">
                            <label>Satuan Kerja</label>
                            <select name="id_lokasi_kerja" id="id_lokasi_kerja" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @php
                                    $lokasis = DB::table('lokasi_kerjas')->get();
                                @endphp
                                @foreach ($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}">{{ $lokasi->lokasi_kerja }}</option>
                                @endforeach
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            id_lokasi_kerja = new TomSelect('#id_lokasi_kerja');

        });

        $("#myTable").DataTable({})

        hapusData = (id) => {

            Swal.fire({
                title: "Yakin hapus data?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                confirmButtonText: "Ya",
                cancelButtonColor: "#3085d6",
                cancelButtonText: "Batal"
            }).then((result) => {

                if (result.value) {

                    axios.post("/delete-lokasi-kerja-user", {
                            id
                        })
                        .then(res => {

                            if (res.data.responCode == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil",
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                window.location.reload();

                            } else {
                                Swal.fire({
                                    icon: "warning",
                                    title: "Gagal",
                                    text: res.data.respon,
                                });
                            }

                        });
                }
            });
        }
    </script>
@endpush
