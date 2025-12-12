document.addEventListener('DOMContentLoaded', function () {
    getData();
});

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
            url: '/data-shift',
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
                    return `<b class="d-md-none">Shift: </b><br class="d-md-none"> ${row.nama_shift}`
                }
            },
            { 
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Jam Masuk </b><br class="d-md-none"> <i class="d-md-none bi bi-clock"></i>
                    ${row.jam_masuk}`
                }
            },
            { 
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Jam Pulang</b><br class="d-md-none"> <i class="d-md-none bi bi-clock"></i>
                    ${row.jam_pulang}`
                }
            },
            { 
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Mulai Scan Masuk</b><br class="d-md-none"> <i class="d-md-none bi bi-clock"></i>
                    ${row.mulai_scan_masuk}`
                }
            },
            { 
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Akhir Scan Masuk</b><br class="d-md-none"> <i class="d-md-none bi bi-clock"></i>
                    ${row.akhir_scan_masuk}`
                }
            },
            { 
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Mulai Scan Pulang</b><br class="d-md-none"> <i class="d-md-none bi bi-clock"></i>
                    ${row.mulai_scan_pulang}`
                }
            },
            { 
                render: function (data, type, row, meta) {
                    return `<b class="d-md-none">Akhir Scan Pulang</b><br class="d-md-none"> <i class="d-md-none bi bi-clock"></i>
                    ${row.akhir_scan_pulang}`
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
                            <a class="dropdown-item text-success" data-toggle="modal" data-target="#modal"
                                href="javascript:void(0)" data-bs-id="${row.id}">
                                <i class="bi bi-grid"></i> Edit
                            </a>
                            <a class="dropdown-item text-danger" onclick="hapusData(${row.id})">
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
$('#modal').on('show.bs.modal', function (event) {

    var button = $(event.relatedTarget); // Button that triggered the modal
    var recipient = button.data('bs-id'); // Extract info from data-* attributes
    var cok = $("#myTable").DataTable().rows().data().toArray();

    let cokData = cok.filter((dt) => {
        return dt.id == recipient;
    });

    document.getElementById("form").reset();
    document.getElementById('id').value = '';

    if (recipient) {

        var modal = $(this);

        modal.find('#id').val(cokData[0].id);
        modal.find('#nama_shift').val(cokData[0].nama_shift);
        modal.find('#jam_masuk').val(cokData[0].jam_masuk);
        modal.find('#jam_pulang').val(cokData[0].jam_pulang);
        modal.find('#mulai_scan_masuk').val(cokData[0].mulai_scan_masuk);
        modal.find('#akhir_scan_masuk').val(cokData[0].akhir_scan_masuk);
        modal.find('#mulai_scan_pulang').val(cokData[0].mulai_scan_pulang);
        modal.find('#akhir_scan_pulang').val(cokData[0].akhir_scan_pulang);
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
        formData.get('id') == "" ? "/store-shift" : "/update-shift",
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

            axios.post("/delete-shift", { 
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
