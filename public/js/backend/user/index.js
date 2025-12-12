document.addEventListener('DOMContentLoaded', function () {
    getData()
})

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
            url: '/data-user',
            data: function (d) {
                d.keyword = $("#searchInput").val(); // kirim keyword ke backend
            }
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: "name" },
            { data: "email" },
            { data: "role" },
            { data: "created_at" },
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
                                <i class="bi bi-grid"></i> &nbsp; Edit
                            </a>
                            <a class="dropdown-item text-danger" onclick="hapusData(${row.id})">
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
        // Edit Mode
        var modal = $(this);
        modal.find('#id').val(cokData[0].id);
        modal.find('#email').val(cokData[0].email);
        modal.find('#name').val(cokData[0].name);
        modal.find('#role').val(cokData[0].role);

    }
});

form.onsubmit = (e) => {

    let formData = new FormData(form);

    document.getElementById('respon_error').innerHTML = ``

    e.preventDefault();

    document.getElementById("tombol_kirim").disabled = true;

    axios({
        method: 'post',
        url: formData.get('id') == '' ? '/store-user' : '/update-user',
        data: formData,
    })
        .then(function (res) {
            //handle success         
            if (res.data.responCode == 1) {

                Swal.fire({
                    icon: 'success',
                    title: 'Sukses',
                    text: res.data.respon,
                    timer: 3000,
                    showConfirmButton: false
                })

                $("#modal").modal("hide");
                $('#myTable').DataTable().clear().destroy();
                getData()

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
            axios.post('/delete-user', {
                id
            })
                .then((response) => {
                    if (response.data.responCode == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            timer: 2000,
                            showConfirmButton: false
                        })

                        $('#myTable').DataTable().clear().destroy();
                        getData();

                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Gagal...',
                            text: response.data.respon,
                        })
                    }
                }, (error) => {
                    console.log(error);
                });
        }

    });
}