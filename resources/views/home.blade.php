@extends('layouts.home')

@section('content')
<main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

        <img src="{{ asset('assets/bghome.avif') }}" alt="" data-aos="fade-in">

        <div class="container">
            <div class="row">
                <div class="col-xl-4">
                    <h1 data-aos="fade-up">Sumber Rejeki Official</h1>
                    <blockquote data-aos="fade-up" data-aos-delay="100">
                        <p>Sumber Rejeki Official adalah website sistem informasi furniture yang dapat dikelola oleh
                            admin, owner, kasir, dan gudang. </p>
                    </blockquote>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                        <!--<a href="produk.php" class="btn-get-started">Produk Kami</a>-->
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /Hero Section -->

    <!-- Why Us Section -->
    <section id="why-us" class="why-us section">

        <div class="container">

            <div class="row g-0">

                <div class="col-xl-5 img-bg" data-aos="fade-up" data-aos-delay="100">
                    <img src="{{ asset('assets/bgkiri.jpg') }}" alt="">
                </div>

                <div class="col-xl-7 slides position-relative" data-aos="fade-up" data-aos-delay="200">

                    <div class="swiper init-swiper">
                        <script type="application/json" class="swiper-config">
                            {
								"loop": true,
								"speed": 600,
								"autoplay": {
									"delay": 5000
								},
								"slidesPerView": "auto",
								"centeredSlides": true,
								"pagination": {
									"el": ".swiper-pagination",
									"type": "bullets",
									"clickable": true
								},
								"navigation": {
									"nextEl": ".swiper-button-next",
									"prevEl": ".swiper-button-prev"
								}
							}
                        </script>
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <div class="item">
                                    <h3 class="mb-3">Kelola Stock dengan Mudah</h3>
                                    <h4 class="mb-3">Sistem yang dirancang untuk memudahkan pencatatan dan pengelolaan
                                        stok barang.</h4>
                                    <p>Sumber Rejeki Official menyediakan fitur yang lengkap, terdiri dari admin, owner,
                                        kasir, dan gudang.
                                        Dapat mengelola stok secara efisien, mengurangi kesalahan pencatatan,
                                        dan meningkatkan akurasi inventaris.</p>
                                </div>
                            </div><!-- End slide item -->

                            <div class="swiper-slide">
                                <div class="item">
                                    <h3 class="mb-3">Hak Akses yang Terstruktur</h3>
                                    <h4 class="mb-3">Atur hak akses pengguna sesuai dengan peran mereka dalam bisnis.
                                    </h4>
                                    <p>Owner, admin, kasir, dan gudang memiliki akses sesuai dengan kebutuhan
                                        operasional
                                        mereka, memastikan keamanan, dan efisiensi dalam pengelolaan data stok. </p>
                                </div>
                            </div><!-- End slide item -->

                            <div class="swiper-slide">
                                <div class="item">
                                    <h3 class="mb-3">Monitoring Stok Real-Time</h3>
                                    <h4 class="mb-3">Pantau stok barang kapan saja dan dimana saja dengan sistem yang
                                        selalu diperbarui. </h4>
                                    <p>Dapatkan laporan stok secara akurat dan langsung, mempermudah pengambilan
                                        keputusan dalam pengadaan barang dan mencegah kehabisan stok. </p>
                                </div>
                            </div><!-- End slide item -->

                            <div class="swiper-slide">
                                <div class="item">
                                    <h3 class="mb-3">Laporan Lengkap dan Akurat</h3>
                                    <h4 class="mb-3">Sajikan data stok dalam laporan yang jelas dan terperinci. </h4>
                                    <p>Owner dan admin dengan mudah mengakses laporan stok harian, bulanan, atau tahunan
                                        untuk analisis bisnis yang lebih baik. </p>
                                </div>
                            </div><!-- End slide item -->

                            <div class="swiper-slide">
                                <div class="item">
                                    <h3 class="mb-3">Transaksi Cepat dan Aman</h3>
                                    <h4 class="mb-3">Proses transaksi yang mudah, akurat, dan terintegrasi dengan
                                        sistem stok. </h4>
                                    <p>Kasir dapat melakukan pencatatan transaksi dengan cepat, sementara stok barang
                                        akan otomatis berkurang sesuai dengan pembelian yang terjadi. Hindari kesalahan
                                        input dan tingkatkan efisiensi operasional toko. </p>
                                </div>
                            </div><!-- End slide item -->

                        </div>

                        <div class="swiper-pagination"></div>
                    </div>

                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>

            </div>

        </div>

    </section>


</main>
@endsection