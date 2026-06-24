@extends('backend.app')
@section('content')
    <style>
        #tableShift {
            width: max-content !important;
            table-layout: auto;
        }

        #tableShift th,
        #tableShift td {
            white-space: nowrap;
            padding: 0.5rem;
            text-align: center;
            font-size: 14px !important;
        }

        #tableShift td:not(:first-child),
        #tableShift th:not(:first-child) {
            width: 40px;
            min-width: 40px;
            max-width: 40px;
        }

        #tableShift td:first-child,
        #tableShift th:first-child {
            text-align: left;
            min-width: 180px;
        }
    </style>
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

                    <div class="input-group mb-3">
                        <input type="date" class="form-control" id="startDate" name="start">
                        <input type="date" class="form-control" id="endDate" name="end">
                        <select class="custom-select" id="idLokasiKerja" name="id_lokasi_kerja">
                            <option selected>Pilih OPD</option>
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
                                <option value="{{ $l->id }}">
                                    {{ $l->lokasi_kerja }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TABLE  --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="tableShift">
                            <thead></thead>
                            <tbody></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        function loadData() {

            $.get('/data-laporan-shift-opd', {

                start: $("#startDate").val(),
                end: $("#endDate").val(),
                id_lokasi_kerja: $("#idLokasiKerja").val()

            }, function(data) {

                if (data.length === 0) {
                    $("#tableShift thead").html('');
                    $("#tableShift tbody").html('');
                    return;
                }

                // ambil nama kolom
                let columns = Object.keys(data[0]);

                // HEADER
                let header = "<tr>";

                columns.forEach(function(col) {

                    if (col == 'nama') {
                        header += "<th>Nama Pegawai</th>";
                    } else if (col != 'nama' && col != 'nip') {
                        header += "<th>" + col.split("-")[2] + "</th>";
                    }

                });

                header += "</tr>";

                $("#tableShift thead").html(header);

                // BODY
                let body = "";

                data.forEach(function(row) {

                    body += "<tr>";

                    columns.forEach(function(col) {

                        if (col == 'nama') {

                            body += `
                        <td style="white-space: nowrap">
                            ${row.nama}<br>
                            <b>NIP. ${row.nip}</b>
                        </td>`;
                        } else if (col != 'nama' && col != 'nip') {

                            let value = row[col] || "";

                            let badge = "";

                            value.split(",").forEach(function(shift) {

                                if (shift == "R")
                                    badge +=
                                    `<span style="border-radius:8px!important" class="badge badge-primary mr-1">R</span>`;

                                if (shift == "S")
                                    badge +=
                                    `<span style="border-radius:8px!important" class="badge badge-warning mr-1">S</span>`;

                                if (shift == "M")
                                    badge +=
                                    `<span style="border-radius:8px!important" class="badge badge-dark mr-1">M</span>`;
                            });

                            body += `<td class="text-center">${badge}</td>`;
                        }

                    });

                    body += "</tr>";

                });

                $("#tableShift tbody").html(body);

            });

        }


        $(function() {

            // load pertama
            loadData();

            // reload otomatis saat filter berubah
            $("#startDate, #endDate, #idLokasiKerja").on("change", function() {

                if (
                    $("#startDate").val() &&
                    $("#endDate").val() &&
                    $("#idLokasiKerja").val()
                ) {
                    loadData();
                }

            });

        });
    </script>
@endpush
