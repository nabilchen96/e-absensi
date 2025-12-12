<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Skydash Admin</title>
    <link rel="stylesheet" href="{{ asset('skydash/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('skydash/css/vertical-layout-light/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('skydash/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo">
                                {{-- <img src="" alt="logo"> --}}
                            </div>
                            <h4>Hello! let's get started</h4>
                            <h6 class="font-weight-light">Sign in to continue.</h6>
                            <form class="pt-3" id="formLogin">
                                <div class="form-group">
                                    <input type="email" class="form-control form-control-lg" name="email"
                                        id="email" placeholder="Username">
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-lg" id="password"
                                        name="password" placeholder="Password">
                                </div>
                                <div class="mt-3">
                                    <button type="submit" id="btnLogin"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                        Login
                                    </button>
                                    <button type="button" id="btnLoginLoading" style="display: none;" disabled
                                        class="w-full py-2 bg-red-400 text-white rounded-md flex items-center justify-center">
                                        <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z" />
                                        </svg>
                                        Loading...
                                    </button>
                                </div>
                                <div class="my-2 d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <label class="form-check-label text-muted">
                                            <input type="checkbox" class="form-check-input">
                                            Keep me signed in
                                        </label>
                                    </div>
                                    <a href="#" class="auth-link text-black">Forgot password?</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>

    <script src="{{ asset('skydash/js/axios/dist/axios.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>
    <script>
        const formLogin = document.getElementById("formLogin");

        formLogin.onsubmit = (e) => {
            e.preventDefault();

            const formData = new FormData(formLogin);
            document.getElementById("btnLogin").style.display = "none";
            document.getElementById("btnLoginLoading").style.display = "flex";

            axios.post('/loginProses', formData)
                .then(res => {
                    if (res.data.responCode == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil',
                            text: 'Mengalihkan ke dashboard...',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Login Gagal',
                            text: res.data.respon
                        });
                    }
                })
                .catch(err => console.error(err))
                .finally(() => {
                    document.getElementById("btnLogin").style.display = "block";
                    document.getElementById("btnLoginLoading").style.display = "none";
                });
        }
    </script>
</body>

</html>
