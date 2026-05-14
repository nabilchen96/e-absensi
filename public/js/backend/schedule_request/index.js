var table = null;
let id_user, id_shift, idUserSearch
const role = window.APP_DATA.role

document.addEventListener('DOMContentLoaded', function () {
    getData();
    id_user = new TomSelect('#id_user');
    idUserSearch = new TomSelect('#idUserSearch');

});

//SEARCHING DATA
$("#btnCari").click(function () {
    table.ajax.reload();

    // Ambil nilai filter
    const user = $("#idUserSearch option:selected").text();
    const idUser = $("#idUserSearch").val();
    const tglDari = $("#tanggalDari").val();
    const tglSampai = $("#tanggalSampai").val();

    // Buat teks filter
    let info = [];

    if (idUser) info.push("User: " + user);
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
            url: '/data-schedule-request',
            data: function (d) {
                d.keyword = $("#searchInput").val();
                d.id_user = $("#idUserSearch").val();
            }
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                render: (data, type, row) => `<b class="d-md-none">Pegawai: </b><br class="d-md-none"> ${row.name}`
            },
            {
                render: (data, type, row) => `<b class="d-md-none">Tgl Jadwal: </b><br class="d-md-none"> ${row.tanggal_awal} → ${row.tanggal_akhir}`
            },
            {
                render: (data, type, row) => `<b class="d-md-none">Status: </b><br class="d-md-none"> ${row.status}`
            },
            {
                render: (data, type, row) => `<b class="d-md-none">Tgl Pengajuan: </b><br class="d-md-none"> ${row.created_at}`
            },
            {
                render: function (data, type, row, meta) {
                    if (!row.file) return `<span class="text-muted">Tidak ada</span>`;
                    return `
                        <a href="/storage/${row.file}" target="_blank" class="text-primary">
                            <i class="bi bi-file-earmark-text"></i> Lihat
                        </a>
                    `;
                },
            },
             {
                render: (data, type, row) => `<b class="d-md-none">Status: </b><br class="d-md-none"> ${row.catatan ?? '-'}`
            },
            {
                render: function (data, type, row) {

                    let konfirmasiBtn = '';

                    konfirmasiBtn = `
                        <a class="dropdown-item text-info"
                        data-toggle="modal" data-target="#modalStatus"
                        href="javascript:void(0)" data-bs-id="${row.id}">
                        <i class="bi bi-check-square"></i> Ubah Status
                        </a>
                    `;

                    return `
                    <div class="dropdown">
                        <a class="text-success" href="#" data-toggle="dropdown">
                            <i class="bi bi-three-dots" style="font-size:1.5rem"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="/schedule?id=${row.id}" class="dropdown-item text-warning"
                                href="javascript:void(0)" data-bs-id="${row.id}">
                                <i class="bi bi-eye"></i> Detail
                            </a>

                             <a class="dropdown-item text-success" data-toggle="modal" data-target="#modal" 
                                href="javascript:void(0)" data-bs-id="${row.id}">
                                <i class="bi bi-grid"></i> Edit
                            </a>

                            ${konfirmasiBtn}

                            <a href="#" class="dropdown-item text-danger" onclick="hapusData(${row.id})">
                            <i class="bi bi-trash"></i> Hapus
                            </a>
                        </div>
                    </div>`;
                }
            }
        ],
    });
}

//MODAL EDIT
$('#modal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var recipient = button.data('bs-id');
    var cok = $("#myTable").DataTable().rows().data().toArray();

    let cokData = cok.filter((dt) => dt.id == recipient);

    document.getElementById("form").reset();
    document.getElementById('id').value = '';
    id_user.setValue('');

    if (recipient) {
        var modal = $(this);
        modal.find('#id').val(cokData[0].id);
        id_user.setValue(cokData[0].id_user);
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
        // modal.find('#catatan').val(cokData[0].catatan);
    }
});

//CREATE OR UPDATE DATA
form.onsubmit = (e) => {

    let formData = new FormData(form);

    document.getElementById('respon_error').innerHTML = ``

    e.preventDefault();

    document.getElementById("tombol_kirim").disabled = true;

    axios({
        method: 'post',
        url: formData.get('id') == '' ? '/store-schedule-request' : '/update-schedule-request',
        data: formData,
    })
        .then(function (res) {

            if (res.data.responCode == 1) {

                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: res.data.respon + '. Anda akan diarahkan ke halaman input data jadwal',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {

                        window.location.href = `/schedule?id=${res.data.data.id}`;
                    }
                });



            } else {
                let respon_error = ``;
                Object.entries(res.data.respon).forEach(([field, messages]) => {
                    messages.forEach(message => {
                        respon_error += `<li>${message}</li>`;
                    });
                });

                document.getElementById('respon_error').innerHTML = respon_error;
            }

            document.getElementById("tombol_kirim").disabled = false;
        })
        .catch(function (res) {
            document.getElementById("tombol_kirim").disabled = false;
            console.log(res);
        });
};

formStatus.onsubmit = function (e) {
    e.preventDefault();

    let formData = new FormData(formStatus);

    $("#respon_error").html("");
    $("#tombol_kirim").prop("disabled", true);

    axios.post(
        "/update-status-schedule-request",
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


//DELETE DATA
hapusData = (id) => {
    Swal.fire({
        title: "Yakin hapus data?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya',
        cancelButtonColor: '#3085d6',
        cancelButtonText: "Batal"
    }).then((result) => {

        if (result.value) {
            axios.post('/delete-schedule-request', { id })
                .then((response) => {

                    if (response.data.responCode == 1) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        $('#myTable').DataTable().clear().destroy();
                        getData();

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal...',
                            text: response.data.respon,
                        });
                    }
                });
        }

    });
};
