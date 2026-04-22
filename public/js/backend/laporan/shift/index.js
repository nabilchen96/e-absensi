var table = null;
let idUserSearch

document.addEventListener('DOMContentLoaded', function () {
    getData();
    idUserSearch = new TomSelect('#idUserSearch');

});

//SEARCHING DATA
$("#btnCari").click(function () {
    table.ajax.reload();

    // Ambil nilai filter
    const user = $("#idUserSearch option:selected").text();
    const tglDari = $("#tanggalDari").val();
    const tglSampai = $("#tanggalSampai").val();

    // Buat teks filter
    let info = [];

    if (tglDari) info.push("Tanggal Dari: " + tglDari);
    if (tglSampai) info.push("Tanggal Sampai: " + tglSampai);

    // Tampilkan info filter jika ada filter
    if (info.length > 0) {
        $("#textFilter").text(info.join(" | "));
        $("#infoFilter").removeClass("d-none");
    }

    $("#modalCari").modal("hide");
});

//RESET FILTER
$("#btnResetFilter").click(function () {

    // Kosongkan form filter
    $("#idUserSearch").val("");
    $("#tanggalDari").val("");
    $("#tanggalSampai").val("");

    // Sembunyikan info filter
    $("#infoFilter").addClass("d-none");
    $("#textFilter").text("");

    // Reload ulang, data default
    table.ajax.reload();
});

//LOAD DATA
function getData() {
    table = $("#myTable").DataTable({
        ordering: true,
        processing: true,
        searching: false,
        lengthChange: false,
        ajax: {
            url: '/data-laporan-shift',
            dataSrc: function (json) {

                // fallback biar gak error
                if (!json.summary) {
                    console.error("Summary tidak ada:", json);
                    return json.data ?? json;
                }

                $("#totalJamKerja").text(json.summary.total_jam_kerja || "00:00:00");
                $("#totalTerlambat").text(json.summary.total_terlambat || "00:00:00");
                $("#totalHadir").text(json.summary.total_hadir || 0);
                $("#totalShift").text(json.summary.total_shift || 0);

                return json.data;
            },
            data: function (d) {
                d.id_user = $("#idUserSearch").val();
                d.start_date = $("#tanggalDari").val();
                d.end_date = $("#tanggalSampai").val();
            }
        },
        createdRow: function (row, data) {

            // Kolom Terlambat (misal index kolom = 3)
            if (data.terlambat && data.terlambat !== "00:00:00") {
                $('td', row).eq(5).addClass('bg-danger text-white');
            }

            // Kolom Pulang Cepat (misal index kolom = 4)
            if (data.pulang_cepat && data.pulang_cepat !== "00:00:00") {
                $('td', row).eq(6).addClass('bg-danger text-white');
            }

        },
        columns: [
            {
                render: (data, type, row) => `
                    <b class="d-md-none">Tanggal: </b>
                    <br class="d-md-none"> ${row.tanggal}`
            },
            {
                render: (data, type, row) => `
                    <span style="white-space: nowrap;"><b class="d-md-none">Pegawai: </b>
                    <br class="d-md-none"> ${row.user}</span>`
            },
            {
                render: (data, type, row) => `
                    <span style="white-space: nowrap;"><b class="d-md-none">Shift: </b>
                    <br class="d-md-none"> ${row.shift}</span>`
            },
            {
                render: (data, type, row) => {
                    const waktu = row.scan_masuk ? row.scan_masuk.split(" ")[1] : "";
                    return `
                        <b class="d-md-none">Scan Masuk: </b>
                        <br class="d-md-none"> ${row.scan_masuk}
                    `;
                }
            },
            {
                render: (data, type, row) => {
                    const waktu1 = row.scan_pulang ? row.scan_pulang.split(" ")[1] : "";
                    return `
                        <b class="d-md-none">Scan Masuk: </b>
                        <br class="d-md-none"> ${row.scan_pulang}
                    `;
                }
            },
            {
                render: (data, type, row) => `
                    <b class="d-md-none">Terlambat: </b>
                    <br class="d-md-none"> ${row.terlambat}`
            },
            // {
            //     render: (data, type, row) => `
            //         <b class="d-md-none">Pulang Cepat: </b>
            //         <br class="d-md-none"> ${row.pulang_cepat}`
            // },
            {
                render: (data, type, row) => `
                    <b class="d-md-none">Total Jam: </b>
                    <br class="d-md-none"> ${row.total_jam}`
            },
            {
                render: (data, type, row) => `
                    <b class="d-md-none">Keterangan: </b>
                    <br class="d-md-none"> ${row.keterangan}`
            }
        ],
    });
}
