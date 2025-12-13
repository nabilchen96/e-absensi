document.addEventListener('DOMContentLoaded', function () {
    getData();
    initCamera();
    getLocation();
});

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

                document.getElementById('respon_error').innerHTML = respon_error

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

            console.log(pos.coords.latitude);

            $("#latitude").val(pos.coords.latitude);
            $("#longitude").val(pos.coords.longitude);
        });
    }
}
