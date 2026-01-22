document.addEventListener('DOMContentLoaded', function () {
    getData();
});

$("#btnCari").click(function () {
    table.ajax.reload();
});

var table = null;
const role = window.APP_DATA.role

function getData() {
    table = $("#myTable").DataTable({
        ordering: true,
        processing: true,
        searching: false,
        lengthChange: false,
        ajax: {
            url: '/data-perizinan',
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
                    return `<b class="d-md-none">User: </b><br class="d-md-none">${row.user_name}`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Tanggal: </b><br class="d-md-none">${row.tanggal}`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Jenis: </b><br class="d-md-none">${row.jenis}`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Status: </b><br class="d-md-none">${row.status}`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Keterangan: </b><br class="d-md-none">${row.keterangan ?? '-'}`;
                }
            },
            {
                render: function (data, type, row, meta) {
                    if (!row.file) return `<span class="text-muted">Tidak ada</span>`;
                    return `
                        <a href="/storage/${row.file}" target="_blank" class="text-primary">
                            <i class="bi bi-file-earmark-text"></i> Lihat
                        </a>
                    `;
                }
            },
            {
                render: function (data, type, row, meta) {


                    if (role == 'OPD') {
                        return `
                            <div class="dropdown">
                                <a class="text-success" href="#" data-toggle="dropdown">
                                    <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item text-info" data-toggle="modal" data-target="#modalStatus"
                                        href="javascript:void(0)" data-bs-id="${row.id}">
                                        <i class="bi bi-grid"></i> Ubah Status
                                    </a>
                                    <a href="javascript:void(0)" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                            `;
                    }

                    if (role == 'Pegawai') {

                        if(row.status == 'Ditolak'){
                            return `
                            <div class="dropdown">
                                <a class="text-success" href="#" data-toggle="dropdown">
                                    <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item text-success" data-toggle="modal" data-target="#modal"
                                        href="javascript:void(0)" data-bs-id="${row.id}">
                                        <i class="bi bi-grid"></i> Edit
                                    </a>
                                    <a href="javascript:void(0)" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                            `;
                        }else{
                            return `
                            <div class="dropdown">
                                <a class="text-success" href="#" data-toggle="dropdown">
                                    <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a href="javascript:void(0)" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                            `;
                        }
                    } else {
                        return `
                            <div class="dropdown">
                                <a class="text-success" href="#" data-toggle="dropdown">
                                    <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item text-success" data-toggle="modal" data-target="#modal"
                                        href="javascript:void(0)" data-bs-id="${row.id}">
                                        <i class="bi bi-grid"></i> Edit
                                    </a>
                                    <a class="dropdown-item text-info" data-toggle="modal" data-target="#modalStatus"
                                        href="javascript:void(0)" data-bs-id="${row.id}">
                                        <i class="bi bi-grid"></i> Ubah Status
                                    </a>
                                    <a href="javascript:void(0)" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                            `;
                    }
                }
            }
        ],
    });
}


// =========================
// SHOW MODAL
// =========================
$('#modal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget);
    var recipient = button.data('bs-id');
    var cok = $("#myTable").DataTable().rows().data().toArray();

    let cokData = cok.filter((dt) => {
        return dt.id == recipient;
    });

    document.getElementById("form").reset();
    document.getElementById('id').value = '';
    $("#previewFile").html("");

    if (recipient) {

        var modal = $(this);

        modal.find('#id').val(cokData[0].id);
        modal.find('#user_id').val(cokData[0].user_id);
        modal.find('#tanggal').val(cokData[0].tanggal);
        modal.find('#jenis').val(cokData[0].jenis);
        modal.find('#keterangan').val(cokData[0].keterangan);

        if (cokData[0].file) {
            $("#previewFile").html(`
                <a href="/storage/${cokData[0].file}" target="_blank">
                    <i class="bi bi-file-earmark-text"></i> Lihat File
                </a>
            `);
        }

    }
});

// =========================
// SHOW MODAL
// =========================
$('#modalStatus').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget);
    var recipient = button.data('bs-id');
    var cok = $("#myTable").DataTable().rows().data().toArray();

    let cokData = cok.filter((dt) => {
        return dt.id == recipient;
    });

    document.getElementById("form").reset();
    document.getElementById('id').value = '';

    if (recipient) {
        // console.log(cokData[0].id, cokData[0].status);
        
        var modal = $(this);
        modal.find('#id').val(cokData[0].id);
        modal.find('#status').val(cokData[0].status);
    }
});


// =========================
// FORM SUBMIT (AJAX)
// =========================
let form = document.getElementById('form');

form.onsubmit = function (e) {
    e.preventDefault();

    let formData = new FormData(form);

    $("#respon_error").html("");
    $("#tombol_kirim").prop("disabled", true);

    axios.post(
        formData.get('id') == "" ? "/store-perizinan" : "/update-perizinan",
        formData
    )
        .then(res => {
            $("#tombol_kirim").prop("disabled", false);

            if (res.data.responCode == 1) {
                Swal.fire({
                    icon: "success",
                    title: "Sukses",
                    text: res.data.respon,
                    showConfirmButton: false,
                    timer: 2000
                });

                $("#modal").modal("hide");
                table.destroy();
                getData();
            } else {
                let err = "";
                Object.entries(res.data.respon).forEach(([field, msg]) => {
                    msg.forEach(m => err += `<li>${m}</li>`);
                });
                $("#respon_error").html(err);
            }
        })
        .catch(err => {
            $("#tombol_kirim").prop("disabled", false);
            console.error(err);
        });
}

formStatus.onsubmit = function (e) {
    e.preventDefault();

    let formData = new FormData(formStatus);

    $("#respon_error").html("");
    $("#tombol_kirim").prop("disabled", true);

    axios.post(
        "/update-status-perizinan",
        formData
    )
        .then(res => {
            $("#tombol_kirim").prop("disabled", false);

            if (res.data.responCode == 1) {
                Swal.fire({
                    icon: "success",
                    title: "Sukses",
                    text: res.data.respon,
                    showConfirmButton: false,
                    timer: 2000
                });

                $("#modalStatus").modal("hide");
                table.destroy();
                getData();
            } else {
                let err = "";
                Object.entries(res.data.respon).forEach(([field, msg]) => {
                    msg.forEach(m => err += `<li>${m}</li>`);
                });
                $("#respon_error").html(err);
            }
        })
        .catch(err => {
            $("#tombol_kirim").prop("disabled", false);
            console.error(err);
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

            axios.post("/delete-perizinan", {
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

                        table.destroy();
                        getData();

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
