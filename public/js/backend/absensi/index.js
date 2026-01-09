document.addEventListener('DOMContentLoaded', function () {
    getData();
    initCamera();
    getLocation();
});

const kantor = window.APP_DATA.lokasiKantor;

const kantorLat = parseFloat(kantor.latitude);
const kantorLng = parseFloat(kantor.longitude);
let map;           // global map variable
let markerUser;

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
        searching: false,
        lengthChange: false,
        ajax: {
            url: '/data-absensi',
            data: function (d) {
                d.keyword = $("#searchInput").val();
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
                        <img src="/storage/absensi/${row.foto}" style="border-radius: 8px; width: 80px; height: 80px; object-fit: fit;">
                    `;
                }
            },
            { render: (d, t, r) => r.user.name },
            {
                render: function (data, type, row, meta) {
                    return `
                        <b class="d-md-none">Latitude, Longitude</b>
                        <br class="d-md-none"> 
                        
                        <span class="d-md-none">${row.latitude}, ${row.longitude}</span>

                        <span class="d-none d-md-inline-block">${row.latitude}</span>
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                        <span class="d-none d-md-inline-block">${row.longitude}</span>
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                        <b class="d-md-none">Waktu Absen: </b><br class="d-md-none"> 
                        ${row.datetime}
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {

                    if (!row.jarak) {
                        return '-';
                    }

                    const jarakAngka = parseFloat(
                        row.jarak.replace(/\./g, '').replace(',', '.')
                    );

                    if (isNaN(jarakAngka)) {
                        return '-';
                    }

                    const hasil = Math.floor(jarakAngka).toLocaleString('id-ID');
                    return `${hasil} Meter`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `
                    <div class="dropdown">
                        <a class="text-success" href="#" data-toggle="dropdown">
                            <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
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
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (pos) {

            const userLat = pos.coords.latitude;
            const userLng = pos.coords.longitude;

            $("#latitude").val(userLat);
            $("#longitude").val(userLng);

            console.log(kantorLat, kantorLng);


            // hitung jarak ke kantor
            const jarak = hitungJarak(
                userLat,
                userLng,
                kantorLat,
                kantorLng
            );

            // console.log("Jarak ke kantor:", jarak.toFixed(2), "meter");

            // validasi perimeter
            // if (jarak <= radiusKantor) {
            //     console.log("✅ DALAM PERIMETER KANTOR");
            //     $("#statusLokasi").val("DALAM");
            // } else {
            //     console.log("❌ DI LUAR PERIMETER KANTOR");
            //     $("#statusLokasi").val("LUAR");
            // }

            // kalau mau ditampilkan ke UI
            $("#jarak").val(
                jarak.toLocaleString('id-ID', {
                    minimumFractionDigits: 3,
                    maximumFractionDigits: 3
                })
            );

        }, function (error) {
            alert("Gagal mengambil lokasi");
        });
    }
}





