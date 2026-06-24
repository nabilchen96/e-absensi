@extends('backend.app')
@section('content')
    <style>

    </style>
    <div class="row" style="margin-top: -200px;">
        <div class="col-md-12 text-white">
            <div class="row">
                <div class="col-12 col-xl-8 mb-xl-0">
                    <h3 class="font-weight-bold">
                        {{ $rekap->lokasi_kerja }}
                    </h3>
                    <h4>
                        📅 {{ $rekap->tanggal_awal }} s/d {{ $rekap->tanggal_akhir }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mt-4">
            <div class="card w-100">
                <div class="card-body">

                    
                   

                    <div class="table-responsive">
                        <table id="myTable" class="table table-striped" style="width: 100%;">
                            <thead class="bg-info text-white">
                                <tr>
                                    <th width="5%"></th>
                                    <th>Nama</th>
                                    <th>Jam Kerja </th>
                                    <th>Total Terlambat</th>
                                    <th>Total Scan Masuk</th>
                                    <th>Total Scan Pulang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $k => $item)
                                    <tr>
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ gmdate('H:i:s', $item->detik_kerja) }}</td>
                                        <td>{{ gmdate('H:i:s', $item->detik_terlambat) }}</td>
                                        <td>{{ $item->total_absensi_masuk }}X</td>
                                        <td>{{ $item->total_absensi_pulang }}X</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $("#myTable").DataTable({})
    </script>
@endpush
