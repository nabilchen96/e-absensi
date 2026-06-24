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

                if (!json.widget) {
                    console.error("Widget tidak ditemukan", json);
                    return [];
                }

                $("#totalJamKerja").text(
                    json.widget.total_durasi_jam_kerja || "00:00"
                );

                $("#totalTerlambat").text(
                    json.widget.total_durasi_terlambat || "00:00"
                );

                $("#totalMasuk").text(
                    json.widget.total_absensi_masuk || 0
                );

                $("#totalPulang").text(
                    json.widget.total_absensi_pulang || 0
                );

                return json.table;
            },
            data: function (d) {
                d.id_user = $("#idUserSearch").val();
                d.start_date = $("#tanggalDari").val();
                d.end_date = $("#tanggalSampai").val();
            }
        },
        createdRow: function (row, data) {

            if (
                data.total_terlambat &&
                data.total_terlambat !== "00:00"
            ) {
                $('td', row).eq(5).addClass('bg-danger text-white');
            }

        },
        columns: [
            {
                data: "tanggal"
            },
            {
                data: "nama_pegawai"
            },
            {
                data: "shift"
            },
            {
                render: function (data, type, row) {

                    let waktu = "";

                    if (row.jam_scan_masuk) {
                        waktu = row.jam_scan_masuk.split(" ")[1];
                    }

                    return `
                <b class="d-md-none">Scan Masuk:</b>
                <br class="d-md-none">
                ${waktu}
            `;
                }
            },
            {
                render: function (data, type, row) {

                    let waktu = "";

                    if (row.jam_scan_pulang) {
                        waktu = row.jam_scan_pulang.split(" ")[1];
                    }

                    return `
                <b class="d-md-none">Scan Pulang:</b>
                <br class="d-md-none">
                ${waktu}
            `;
                }
            },
            {
                data: "total_terlambat"
            },
            {
                data: "total_jam_kerja"
            },
            // {
            //     render: function(data, type, row){
            //         if(row.total_jam_kerja === "00:00"){
            //             return `Invalid`
            //         }else{
            //             return `Valid`
            //         }
            //     }
            // }
        ]
    });
}
