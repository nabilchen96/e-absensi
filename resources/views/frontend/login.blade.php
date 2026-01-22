<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="login-form-02/https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="login-form-02/fonts/icomoon/style.css">

    <link rel="stylesheet" href="login-form-02/css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="login-form-02/css/bootstrap.min.css">

    <!-- Style -->
    <link rel="stylesheet" href="login-form-02/css/style.css">
    <link rel="shortcut icon" href="https://poltekbangplg.ac.id/wp-content/uploads/2020/06/favicon.ico"
        type="image/x-icon" />
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
                        <h3>Login to <br><strong>Aplikasi E-Absensi<br> Bengkulu Utara</strong></h3>
                        <br>
                        <form id="formLogin">
                            <div class="form-group first">
                                <label for="username">Email</label>
                                <input type="email" class="form-control" placeholder="your-email@gmail.com"
                                    id="email" name="email">
                            </div>
                            <div class="form-group last mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" placeholder="Your Password"
                                    id="password">
                            </div>

                            <div class="d-flex mb-5 align-items-center">
                                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                                    <input type="checkbox" checked="checked" />
                                    <div class="control__indicator"></div>
                                </label>
                                {{-- <span class="ml-auto"><a href="login-form-02/#" class="forgot-pass">Forgot Password</a></span>  --}}
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="btnLogin" class="btn btn-primary btn-lg btn-block">Sign
                                    in</button>

                                <button style="display: none; background: #0d6efd;" id="btnLoginLoading"
                                    class="btn btn-info btn-moodle text-white btn-lg btn-block" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status"
                                        aria-hidden="true"></span>

                                </button>
                                {{-- <a style="text-decoration: none;" href="{{ url('absen-tanpa-login') }}"
                                    class="text-white btn btn-info btn-lg btn-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-upc-scan" viewBox="0 0 16 16">
                                        <path
                                            d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5M3 4.5a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0zm2 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 1 0v7a.5.5 0 0 1-1 0z" />
                                    </svg> &nbsp;
                                    Absen tanpa login
                                </a> --}}
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
        formLogin.onsubmit = (e) => {

            e.preventDefault();

            const formData = new FormData(formLogin);
            document.getElementById(`btnLogin`).style.display = "none";
            document.getElementById(`btnLoginLoading`).style.display = "block";

            axios({
                    method: 'post',
                    url: '/loginProses',
                    data: formData,
                })
                .then(function(res) {
                    //handle success
                    if (res.data.responCode == 1) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Login',
                            timer: 1000,
                            text: 'Anda akan diarahkan ke halaman dashboard',
                            showConfirmButton: false,
                            // text: res.data.respon
                        })

                        setTimeout(() => {
                            location.reload(res.data.respon);
                        }, 1500);

                    } else {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Ada kesalahan',
                            text: `${res.data.respon}`,
                        })
                    }
                })
                .catch(function(res) {
                    //handle error
                    console.log(res);
                }).then(function() {
                    // always executed              
                    document.getElementById(`btnLogin`).style.display = "block";
                    document.getElementById(`btnLoginLoading`).style.display = "none";

                });

        }
    </script>

</body>

</html>
