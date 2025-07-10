<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Sumber Rejeki</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link rel="shortcut icon" href="{{ url('/') }}/assets/logo.png" />

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link href="{{ url('assets') }}/home/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ url('assets') }}/home/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ url('assets') }}/home/assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="{{ url('assets') }}/home/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="{{ url('assets') }}/home/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <link href="{{ url('assets') }}/home/assets/css/main.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">


</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

            <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                <!-- Uncomment the line below if you also wish to use an image logo -->
                <!-- <img src="assets/img/logo.png" alt=""> -->
                <h1 class="sitename">Sumber Rejeki</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="{{ url('/') }}" class="">Home<br></a></li>

                    <!-- <li><a href="daftar.php">Daftar</a></li> -->
                    <li><a href="{{ url('login') }}">Login</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>

    @yield('content')

    <footer id="footer" class="footer light-background">

        <div class="footer-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-5 col-md-12 footer-about">
                        <a href="index.html" class="logo d-flex align-items-center">
                            <span class="sitename">Sumber Rejeki Official</span>
                        </a>
                        <p>Menjual Berbagai Produk Furniture</p>
                        <div class="social-links d-flex mt-4">
                            <a href=""><i class="bi bi-twitter-x"></i></a>
                            <a href=""><i class="bi bi-facebook"></i></a>
                            <a href=""><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>


                    <div class="col-lg-4 col-md-12 footer-contact text-center text-md-start">
                        <h4>Kontak</h4>
                        <p>Jl. Gajah Mada No.197, Barat Makam Pahlawan, Rambipuji, Jember </p>

                        <p class="mt-4"><strong>Phone:</strong> <span>+62 82140355834</span></p>
                        <p><strong>Email:</strong> <span>sumberrejeki@gmail.com</span></p>
                    </div>

                </div>
            </div>
        </div>

        <div class="container copyright text-center">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Sumber Rejeki</strong> <span>All Rights
                    Reserved</span></p>

        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ url('assets') }}/home/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('assets') }}/home/assets/vendor/php-email-form/validate.js"></script>
    <script src="{{ url('assets') }}/home/assets/vendor/aos/aos.js"></script>
    <script src="{{ url('assets') }}/home/assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="{{ url('assets') }}/home/assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="{{ url('assets') }}/home/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
    <script src="{{ url('assets') }}/home/assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

    <!-- Main JS File -->
    <script src="{{ url('assets') }}/home/assets/js/main.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.all.min.js"></script>

    @if (session('success'))
    <script>
        Swal.fire({
                title: "Sukses!",
                text: "{{ session('success') }}",
                icon: "success"
            });
    </script>
    @endif
    @if (session('error'))
    <script>
        Swal.fire({
                title: "Oops!",
                text: "{{ session('error') }}",
                icon: "error"
            });
    </script>
    @endif

</body>

</html>