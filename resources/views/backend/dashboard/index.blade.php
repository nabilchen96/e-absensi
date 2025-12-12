@extends('backend.app')
@section('content')
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">Dashboard</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mt-4">
            <div class="card shadow">
                <div class="card-body">
                    <p class="mb-2" style="line-height: 1.5rem;">
                        <b>GRAFIK JAM SCAN PEGAWAI</b><br>
                        🗓️ {{ date('d-m-Y') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-12 mt-4">
            <div class="card shadow">
                <div class="card-body">
                    <p class="mb-2" style="line-height: 1.5rem;">
                        <b>GRAFIK CUTI DAN IZIN PEGAWAI</b><br>
                        🗓️ {{ date('d-m-Y') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mt-4">
            <div class="card w-100 shadow" style="height: 400px !important;">
                <div class="card-body">
                    <p class="mb-2" style="line-height: 1.5rem;">
                        <b>DATA PEGAWAI IZIN HARI INI</b><br>
                        🗓️ {{ date('d-m-Y') }}
                    </p>
                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>User</th>
                                    <th>Jenis</th>
                                    <th width="25%">Keterangan</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($izin as $i)
                                    <tr>
                                        <td>{{ $i->name }}</td>
                                        <td>{{ $i->jenis }}</td>
                                        <td>{{ $i->keterangan }}</td>
                                        <td>
                                            <a href="/storage/{{ $i->file }}" target="_blank" class="text-primary">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <i>Belum Ada Data</i>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-lg-6 mt-4">
            <div class="card w-100 shadow" style="height: 400px !important;">
                <div class="card-body">
                    <p class="mb-2" style="line-height: 1.5rem;">
                        <b>DATA PEGAWAI CUTI HARI INI</b><br>
                        🗓️ {{ date('d-m-Y') }}
                    </p>
                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th>User</th>
                                    <th>Jenis</th>
                                    <th width="25%">Keterangan</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cuti as $i)
                                    <tr>
                                        <td>{{ $i->name }}</td>
                                        <td>{{ $i->jenis }}</td>
                                        <td>{{ $i->keterangan }}</td>
                                        <td>
                                            <a href="/storage/{{ $i->file }}" target="_blank" class="text-primary">
                                                <i class="bi bi-file-earmark-text"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <i>Belum Ada Data</i>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
