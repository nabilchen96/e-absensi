<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="login-form-02/fonts/icomoon/style.css">

    <link rel="stylesheet" href="login-form-02/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="login-form-02/css/bootstrap.min.css">

    <!-- Style -->
    <link rel="stylesheet" href="login-form-02/css/style.css">
    <title>E-ABSENSI Bengkulu Utara</title>
</head>

<body>


    <div class="d-lg-flex half">
        <div class="bg order-1 order-md-2"
            style="background-image: url('https://cdn.pixabay.com/photo/2022/10/03/23/41/house-7497002_640.png');">
        </div>
        <div class="contents order-2 order-md-1">

            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-7">
                        <ul id="respon_error" class="text-danger mb-4"></ul>
                        <form id="formAbsensi">
                            <video style="border: 1px solid grey; border-radius: 8px;" id="camera" width="100%"
                                height="260" autoplay></video>
                            {{-- <canvas style="border: 1px solid grey; border-radius: 8px;" id="canvas" width="100%" height="260" class="d-none"></canvas> --}}
                            <canvas id="canvas" width="350" height="260" class="d-none"></canvas>
                            <input type="hidden" id="foto" name="foto">
                            <br>
                            <button style="border-radius: 8px !important;" type="button"
                                class="btn-sm btn-block btn btn-warning mt-2" id="btnCapture">
                                Ambil Foto
                            </button>
                            <div class="row">
                                <div class="col-lg-6 mt-3">
                                    <div class="form-group last mb-3">
                                        <label>Latitude</label>
                                        <input type="text" class="form-control" name="latitude" id="latitude"
                                            placeholder="Latitude" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 mt-3">
                                    <div class="form-group last mb-3">
                                        <label>Longitude</label>
                                        <input type="text" class="form-control" name="longitude" id="longitude"
                                            placeholder="Longitude" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group first">
                                        <label for="username">Email</label>
                                        <input type="email" class="form-control" placeholder="your-email@gmail.com"
                                            id="email" name="email">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group last mb-3">
                                        <label for="password">Password</label>
                                        <input type="password" class="form-control" name="password"
                                            placeholder="Your Password" id="password">
                                    </div>
                                </div>
                            </div>


                            <div class="d-grid">
                                <button type="submit" id="btnLogin" class="btn btn-info btn-lg btn-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-upc-scan" viewBox="0 0 16 16">
                                        <path
                                            d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                                    </svg> &nbsp;

                                    Absen
                                </button>

                                <button style="display: none; background: #0d6efd;" id="btnLoginLoading"
                                    class="btn btn-info btn-moodle text-white btn-lg btn-block" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>

                                </button>

                            </div>
                            <br>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initCamera();
            getLocation();
        });

        formAbsensi.onsubmit = function(e) {
            e.preventDefault();

            const formData = new FormData(formAbsensi);
            document.getElementById(`btnLogin`).style.display = "none";
            document.getElementById(`btnLoginLoading`).style.display = "block";

            document.getElementById('respon_error').innerHTML = ``


            axios({
                    method: 'post',
                    url: '/store-absen-tanpa-login',
                    data: formData,
                })
                .then(function(res) {

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

                    // always executed              
                    document.getElementById(`btnLogin`).style.display = "block";
                    document.getElementById(`btnLoginLoading`).style.display = "none";
                })
                .catch(function(res) {
                    //handle error
                    console.log(res);

                    // always executed              
                    document.getElementById(`btnLogin`).style.display = "block";
                    document.getElementById(`btnLoginLoading`).style.display = "none";
                });
        }

        // =========================
        // CAMERA
        // =========================
        function initCamera() {
            const camera = document.getElementById("camera");

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(function(stream) {
                        camera.srcObject = stream;
                    });
            }
        }

        document.getElementById("btnCapture").addEventListener("click", function() {

            let camera = document.getElementById("camera");
            let canvas = document.getElementById("canvas");

            let ctx = canvas.getContext("2d");
            ctx.drawImage(camera, 0, 0, 350, 260);

            let image = canvas.toDataURL("image/png");

            // isi input hidden
            document.getElementById("foto").value = image;

            // tampilkan preview hanya jika ada gambar
            if (image) {
                let preview = document.getElementById("previewFoto");
                preview.src = image;
                preview.style.display = "block"; // atau "" bila sebelumnya disembunyikan oleh CSS
            }

        });

        // =========================
        // GET LOCATION
        // =========================
        function getLocation() {
            if (navigator.geolocation) {

                navigator.geolocation.getCurrentPosition(function(pos) {

                    console.log(pos.coords.latitude);

                    // TANPA jQuery
                    document.getElementById('latitude').value = pos.coords.latitude;
                    document.getElementById('longitude').value = pos.coords.longitude;

                }, function(error) {
                    console.error("Error:", error);
                });

            } else {
                console.log("Geolocation tidak didukung browser.");
            }
        }
    </script>
</body>

</html>
