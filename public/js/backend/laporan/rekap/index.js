document.addEventListener('DOMContentLoaded', function () {
    getData();
});

$("#btnCari").click(function () {
    table.ajax.reload(); // reload table dengan keyword baru
});

var table = null;

function getData() {
    table = $("#myTable").DataTable({
        ordering: true,
        processing: true,
        searching: false,
        lengthChange: false,
        ajax: {
            url: '/data-laporan-rekap',
             data: function (d) {
                d.keyword = $("#id_opd_pandu").val(); // kirim keyword ke backend
            }
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                render: (data, type, row) => `
                    <span style="white-space: nowrap;"><b class="d-md-none">Lokasi Kerja: </b>
                    <br class="d-md-none"> ${row.lokasi_kerja}</span>`
            },
            {
                render: (data, type, row) => `
                    <span style="white-space: nowrap;"><b class="d-md-none">Judul Laporan: </b>
                    <br class="d-md-none"> ${row.judul_laporan}</span>`
            },
            {
                render: (data, type, row) => `
                    <b class="d-md-none">Tanggal rekap: </b>
                    <br class="d-md-none"> ${row.tanggal_awal} → ${row.tanggal_akhir}`
            },
            {
                render: (data, type, row) => `
                    <b class="d-md-none">Status: </b>
                    <br class="d-md-none"> ${row.status}`
            },

            {
                render: (data, type, row) => `
                    <b class="d-md-none">Tanggal Buat: </b>
                    <br class="d-md-none"> ${row.created_at}`
            },
            {
                render: function (data, type, row, meta) {
                    return `
                    <div class="dropdown">
                        <a class="text-success" href="#" data-toggle="dropdown">
                            <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a target="_blank" href="/detail-laporan-rekap?batch_id=${row.id}" class="dropdown-item text-info">
                                <i class="bi bi-grid"></i> &nbsp; Detail
                            </a>
                             <a href="#" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                <i class="bi bi-trash"></i> &nbsp; Hapus
                            </a>
                        </div>
                    </div>
                    `;
                }
            }
        ],
    });
}

