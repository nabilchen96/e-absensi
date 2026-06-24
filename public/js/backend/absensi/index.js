document.addEventListener('DOMContentLoaded', function () {
    getData();
    initCamera();
    getLocation();

    $("#jarak").val(
        jarak.toLocaleString('id-ID', {
            minimumFractionDigits: 3,
            maximumFractionDigits: 3
        })
    );
});

$("#filterShift, #filterStatus").on("change", function () {
    table.ajax.reload();
});

$("#jenis_absensi").on("change", function () {

    if ($(this).val() == "") {

        $("#form_absensi_detail").hide();

    } else {

        $("#form_absensi_detail").show();

        getLocation();
    }

});

function cekStatusAbsensi(jarak = null) {

    const jenis = $("#jenis_absensi").val();

    if (!jenis) return;

    const sekarang = new Date();

    const menitSekarang =
        sekarang.getHours() * 60 +
        sekarang.getMinutes();

    let shiftReguler = false;

    if (jenis === "masuk") {

        const mulai = (7 * 60) + 30;
        const selesai = (12 * 60);

        shiftReguler =
            menitSekarang >= mulai &&
            menitSekarang <= selesai;

    } else if (jenis === "pulang") {

        const mulai = (16 * 60);
        const selesai = (18 * 60);

        shiftReguler =
            menitSekarang >= mulai &&
            menitSekarang <= selesai;
    }

    let dalamArea = true;

    if (jarak !== null) {
        dalamArea = jarak <= 50;
    }

    $("#status_shift").val(
        shiftReguler
            ? "reguler"
            : "non_reguler"
    );

    $("#status_lokasi").val(
        dalamArea
            ? "dalam_area"
            : "luar_area"
    );

    const wajibBukti =
        !shiftReguler ||
        !dalamArea;

    if (wajibBukti) {

        $("#field_non_reguler").show();

        $("#bukti").prop("required", true);

        $("#alasan").prop("required", true);

    } else {

        $("#field_non_reguler").hide();

        $("#bukti").prop("required", false);

        $("#alasan").prop("required", false);

    }

}

const kantor = window.APP_DATA.lokasiKantor;
const role   = window.APP_DATA.role;

let kantorLat = null;
let kantorLng = null;
let map;           // global map variable
let markerUser;

$("#id_lokasi_kerja").on("change", function () {

    const selected = $(this).find(":selected");

    kantorLat = parseFloat(selected.data("lat"));
    kantorLng = parseFloat(selected.data("lng"));

    console.log(kantorLat, kantorLng);

    getLocation()

});


function hitungJarak(lat1, lng1, lat2, lng2) {

    // validasi data
    if (
        lat1 == null || lng1 == null ||
        lat2 == null || lng2 == null ||
        isNaN(lat1) || isNaN(lng1) ||
        isNaN(lat2) || isNaN(lng2)
    ) {
        return null; // atau '' jika mau
    }

    const R = 6371000; // meter
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLng / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}



// tombol cari (jika nanti ditambahkan input search)
$("#btnCari").click(function () {
    table.ajax.reload();
});

var table = null;

function getData() {
    table = $("#myTable").DataTable({
        ordering: true,
        processing: true,
        searching: true,
        lengthChange: false,
        ajax: {
            url: '/data-absensi',
            data: function (d) {
                d.keyword = $("#searchInput").val();
                d.shift = $("#filterShift").val();
                d.status_absensi = $("#filterStatus").val();
            }
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                        <img src="/absensi/${row.foto}" style="border: grey 1px solid; border-radius: 8px; width: 80px; height: 80px; object-fit: fit;">
                        <br><br>
                        <b>Pegawai:</b><br>
                        ${row.name}<br>
                        <b>${row.nip}</b>
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                        <b>Longitude:</b><br>
                        ${row.longitude}<br><br>

                        <b>Latitude:</b><br>
                        ${row.latitude}<br><br>

                        <b>Jarak: </b></br>
                        ${row.jarak ?? '-'}
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                        <b>Jenis Absensi: </b><br> 
                        ${row.jenis_absensi ?? '-'}<br></br>

                        <b>Identifikasi Shift: </b></br>
                        ${row.status_shift ?? 'Belum Dicek'}<br></br>

                        <b>Alasan: </b><br> 
                        ${row.alasan ?? '-'}
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                    <b>Status Absen:</b><br>
                    ${row.status_absensi ?? ''}<br><br>
                    
                    
                    <b>Waktu Absen: </b><br> 
                    ${row.datetime}<br><br>
                    
                    
                    <b>Bukti: </b><br> 
                    <a href="/bukti-absensi/${row.bukti}">Bukti Dukung</a>`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    
                    let tombolVerifikasi = '';

                    if (role !== 'Pegawai') {
                        tombolVerifikasi = `
                            <a href="#" class="dropdown-item text-success" onclick="verifikasiData(${row.id})">
                                <i class="bi bi-patch-check"></i> Terima Absensi
                            </a>
                        `;
                    }

                    return `
                    <div class="dropdown">
                        <a class="text-success" href="#" data-toggle="dropdown">
                            <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                            ${tombolVerifikasi}
                        </div>
                    </div>
                    `;
                }
            }
        ],
    });
}

// =========================
// SHOW MODAL
// =========================
$('#modalAbsensi').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget);
    var recipient = button.data('bs-id');
    var allData = $("#myTable").DataTable().rows().data().toArray();

    let selected = allData.filter(dt => dt.id == recipient);

    document.getElementById("formAbsensi").reset();
    document.getElementById('id').value = '';
    $("#previewFoto").attr("src", "");
    $("#foto").val("");
    getLocation();

    if (recipient) {

        var modal = $(this);

        modal.find('#id').val(selected[0].id);
        // modal.find('#latitude').val(selected[0].latitude);
        // modal.find('#longitude').val(selected[0].longitude);

        $("#previewFoto").attr("src", "/storage/absensi/" + selected[0].foto);
    }
});

// =========================
// FORM SUBMIT (AJAX)
// =========================
let form = document.getElementById('formAbsensi');

form.onsubmit = function (e) {
    let formData = new FormData(form);

    document.getElementById('respon_error').innerHTML = ``

    e.preventDefault();

    document.getElementById("tombol_kirim").disabled = true;

    axios({
        method: 'post',
        url: 'store-absensi',
        data: formData,
    })
        .then(function (res) {

            console.log(res.data.responCode);

            if (res.data.responCode == 1) {

                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: res.data.respon,
                    timer: 3000,
                    showConfirmButton: false
                })

                location.reload();

            } else {

                //respon 
                let respon_error = ``
                Object.entries(res.data.respon).forEach(([field, messages]) => {
                    messages.forEach(message => {
                        respon_error += `<li>${message}</li>`;
                    });
                });

                document.getElementById('respon_error').innerHTML = `<ul>` + respon_error + `</ul>`

            }

            document.getElementById("tombol_kirim").disabled = false;

        })
        .catch(function (res) {
            document.getElementById("tombol_kirim").disabled = false;
            //handle error
            console.log(res);
        });
}

// =========================
// HAPUS DATA
// =========================
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

            axios.post("/delete-absensi", { id })
                .then(res => {

                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    table.destroy();
                    getData();

                });
        }
    });
}

verifikasiData = (id) => {

    Swal.fire({
        title: "Yakin Terima Data Absensi?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        confirmButtonText: "Ya",
        cancelButtonColor: "#3085d6",
        cancelButtonText: "Batal"
    }).then((result) => {

        if (result.value) {

            axios.post("/verifikasi-absensi", { id })
                .then(res => {

                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        timer: 2000,
                        showConfirmButton: false
                    });

                    table.destroy();
                    getData();

                });
        }
    });
}

// =========================
// CAMERA
// =========================
function initCamera() {
    const camera = document.getElementById("camera");

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(function (stream) {
                camera.srcObject = stream;
            });
    }
}

$("#btnCapture").click(function () {
    let camera = document.getElementById("camera");
    let canvas = document.getElementById("canvas");

    let ctx = canvas.getContext("2d");
    ctx.drawImage(camera, 0, 0, 350, 260);

    let image = canvas.toDataURL("image/png");

    $("#foto").val(image);
    // tampilkan preview hanya jika ada gambar
    if (image) {
        $("#previewFoto").attr("src", image).show();
    }
});



// =========================
// GET LOCATION
// =========================
function getLocation() {
    if (!navigator.geolocation) {
        alert("Browser tidak mendukung GPS");
        return;
    }

    navigator.geolocation.getCurrentPosition(function (pos) {

        const userLat = pos.coords.latitude;
        const userLng = pos.coords.longitude;

        $("#latitude").val(userLat);
        $("#longitude").val(userLng);

        // ===============================
        // VALIDASI LOKASI KERJA DIPILIH
        // ===============================
        if (
            !kantorLat || !kantorLng ||
            isNaN(kantorLat) || isNaN(kantorLng)
        ) {
            // kosongkan jika "Pilih Lokasi Kerja"
            $("#jarak").val("");
            $("#notifikasi_jarak").html("");
            return; // stop proses
        }

        // ===============================
        // HITUNG JARAK
        // ===============================
        const jarak = hitungJarak(
            userLat,
            userLng,
            parseFloat(kantorLat),
            parseFloat(kantorLng)
        ); // meter

        // tampilkan ke input jarak
        $("#jarak").val(
            jarak.toLocaleString('id-ID', {
                minimumFractionDigits: 3,
                maximumFractionDigits: 3
            })
        );

        // cek reguler / non reguler
        cekStatusAbsensi(jarak);

        // ===============================
        // NOTIFIKASI JARAK
        // ===============================
        if (jarak > 50) {
            $("#notifikasi_jarak").html(
                `Jarak dari kantor adalah <b>${jarak.toFixed(2)} meter</b>, Anda <b>tidak berada di lokasi kerja</b>.`
            ).removeClass("text-success").addClass("text-danger");
        } else {
            $("#notifikasi_jarak").html(
                `Jarak dari kantor adalah <b>${jarak.toFixed(2)} meter</b>, Anda <b>berada di lokasi kerja</b>.`
            ).removeClass("text-danger").addClass("text-success");
        }

    }, function () {
        alert("Gagal mengambil lokasi");
    });
}






