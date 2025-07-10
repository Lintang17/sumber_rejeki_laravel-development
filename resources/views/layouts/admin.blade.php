<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sumber Rejeki</title>
    <!-- base:css -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/admin/assets_admin/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ url('/') }}/assets/admin/assets_admin/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('/') }}/assets/admin/assets_admin/css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ url('/') }}/assets/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="{{ url('/') }}/assets/admin/assets/DataTables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.17.2/dist/sweetalert2.min.css">

    <!-- Tambahkan custom style di sini -->
<style>
    th {
        font-weight: bold !important;
    }
</style>

</head>
<?php
if (auth()->user()->role == 'Admin') {
    $bg = 'bg-primary';
} elseif (auth()->user()->role == 'Owner') {
    $bg = 'bg-success';
} elseif (auth()->user()->role == 'Kasir') {
    $bg = 'bg-info';
} elseif (auth()->user()->role == 'Gudang') {
    $bg = 'bg-warning';
}
?>



<body>
    <div class="container-scroller d-flex">
        <!-- partial:./partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar" style="background-color:  #A97474; color: #2C2C2C;">
            <ul class="nav">
                <li class="nav-item sidebar-category">
                    <p style="color: #F5F5F5; font-weight: bold;">Menu</p>
                    <span></span>
                </li>

                @php
                if (auth()->user()->role == 'Admin') {
                $dashboard = 'admin';
                } elseif (auth()->user()->role == 'Kasir') {
                $dashboard = 'kasir';
                } elseif (auth()->user()->role == 'Owner') {
                $dashboard = 'owner';
                } elseif (auth()->user()->role == 'Gudang') {
                $dashboard = 'gudang';
                }
                @endphp
                <li class="nav-item">
                    <a class="nav-link" href="{{ url($dashboard) }}" style="color: #F5F5F5;">
                        <i class="menu-icon mdi mdi-view-dashboard text-white"></i>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>

                <!-- ADMIN -->

                <?php if (auth()->user()->role == 'Admin') { ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#produksiOwner" aria-expanded="false" aria-controls="produksiOwner">
                            <i class="mdi mdi-factory menu-icon"></i>
                            <span class="menu-title">Data Barang Produksi</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="produksiOwner">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('admin/produksidaftar') }}">Daftar Produksi</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                        <li class="nav-item {{ Request::is('admin/showroomtambah') || Request::is('admin/showroomdaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#showroom-menu" aria-expanded="false" aria-controls="showroom-menu">
                                <i class="mdi mdi-store menu-icon"></i>
                                <span class="menu-title">Barang Masuk Showroom</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse {{ Request::is('admin/showroomtambah') || Request::is('admin/showroomdaftar') ? 'show' : '' }}" id="showroom-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item {{ Request::is('admin/showroomtambah') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('admin/showroomtambah') }}">Tambah Barang</a>
                                    </li>
                                    <li class="nav-item {{ Request::is('admin/showroomdaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('admin/showroomdaftar') }}">Daftar Barang Showroom</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ Request::is('admin/penjualandaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#penjualan" aria-expanded="false" aria-controls="penjualan">
                                <i class="mdi mdi-cart menu-icon"></i>
                                <span class="menu-title">Transaksi Penjualan</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="penjualan">
                                <ul class="nav flex-column sub-menu">
                                    {{-- <li class="nav-item {{ Request::is('kasir/penjualantambah') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('kasir/penjualantambah') }}">Tambah Penjualan</a>
                                    </li> --}}
                                    <li class="nav-item {{ Request::is('admin/penjualandaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('admin/penjualandaftar') }}">Daftar Penjualan</a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        {{-- <li class="nav-item {{ Request::is('admin/laporan/*') ? 'active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#laporanAdmin" aria-expanded="false" aria-controls="laporanAdmin">
                                    <i class="mdi mdi-file-document-box menu-icon"></i>
                                    <span class="menu-title">Laporan Admin</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="collapse {{ Request::is('admin/laporan/*') ? 'show' : '' }}" id="laporanAdmin">
                                    <ul class="nav flex-column sub-menu">
                                        {{-- <li class="nav-item">
                                            <a class="nav-link {{ Request::is('admin/laporan/showroom') ? 'active' : '' }}" href="{{ url('gudang/laporan/showroom') }}">Laporan Barang Showroom</a>
                                        </li> --}}
                                        {{-- <li class="nav-item">
                                            <a class="nav-link {{ Request::is('gudang/laporan/penjualan') ? 'active' : '' }}" href="{{ url('gudang/laporan/penjualan') }}">Laporan Transaksi Penjualan</a>
                                        </li>
                                    </ul>
                                </div>
                            </li> --}}



                    {{-- <li class="nav-item {{ Request::is('admin/barangmasuktambah') || Request::is('admin/barangmasukdaftar') ? 'active' : '' }}">
                        <a class="nav-link" data-toggle="collapse" href="#barangMasuk" aria-expanded="false" aria-controls="barangMasuk">
                            <i class="mdi mdi-truck-delivery menu-icon"></i>
                            <span class="menu-title">Barang Masuk</span>
                            <i class="menu-arrow"></i>
                        </a>
                    <div class="collapse {{ Request::is('admin/barangmasuktambah') || Request::is('admin/barangmasukdaftar') ? 'show' : '' }}" id="barangMasuk">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item {{ Request::is('admin/barangmasuktambah') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('admin/barangmasuktambah') }}">
                                        Tambah Barang Masuk
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/barangmasukdaftar') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ url('admin/barangmasukdaftar') }}">
                                        Daftar Barang Masuk
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li> --}}


                <!-- <li class="nav-item">
            <a class="nav-link" href="pembelian.php">
              <i class="mdi mdi-view-headline menu-icon"></i>
              <span class="menu-title">Transaksi</span>
            </a>
          </li> -->
                {{--<li class="nav-item {{ Request::is('admin/barangkeluardaftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#jual" aria-expanded="false" aria-controls="jual">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Barang Keluar</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="jual">
                        <ul class="nav flex-column sub-menu">
                            <!-- <li class="nav-item"> <a class="nav-link" href="{{ url('admin/barangkeluartambah') }}">Tambah Barang Keluar</a></li> -->
                            <li class="nav-item {{ Request::is('admin/barangkeluardaftar') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('admin/barangkeluardaftar') }}">Daftar Barang
                                    Keluar</a></li>
                        </ul>
                    </div>
                </li> --}}

                {{-- <li class="nav-item {{ Request::is('admin/barangcustom/tambah') || Request::is('admin/barangcustom/daftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#barangcustom-menu" aria-expanded="false" aria-controls="barangcustom-menu">
                        <i class="mdi mdi-cart-outline menu-icon"></i>
                        <span class="menu-title">Barang Custom</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse {{ Request::is('admin/barangcustom/tambah') || Request::is('admin/barangcustom/daftar') ? 'show' : '' }}" id="barangcustom-menu">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item {{ Request::is('admin/barangcustom/tambah') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('admin/barangcustom/tambah') }}">Tambah Barang Custom</a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/barangcustom/daftar') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('admin/barangcustom/daftar') }}">Daftar Barang Custom</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}


                {{-- <li class="nav-item {{ Request::is('admin/stockopnamedaftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#stok" aria-expanded="false" aria-controls="stok">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Stock Opname</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="stok">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item {{ Request::is('admin/stockopnamedaftar') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('admin/stockopnamedaftar') }}">Daftar Stock
                                    Opname</a></li>
                        </ul>
                    </div>
                </li> --}}

                <!-- Data Supplier
<li class="nav-item {{ Request::is('admin/supplier/tambah') || Request::is('admin/supplier/daftar') ? 'active' : '' }}">
    <a class="nav-link" data-toggle="collapse" href="#supplier-menu" aria-expanded="false"
        aria-controls="supplier-menu">
        <i class="mdi mdi-book menu-icon"></i>
        <span class="menu-title">Data Supplier</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="supplier-menu">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ Request::is('admin/supplier/tambah') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('admin/supplier/tambah') }}">Tambah Supplier</a>
            </li>
            <li class="nav-item {{ Request::is('admin/supplier/daftar') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('admin/supplier') }}">Daftar Supplier</a>
            </li>
        </ul>
    </div>
</li> -->

                <!-- Data Kategori
<li class="nav-item {{ Request::is('admin/kategori/tambah') || Request::is('admin/kategoridaftar') ? 'active' : '' }}">
    <a class="nav-link" data-toggle="collapse" href="#kategori-menu" aria-expanded="false"
        aria-controls="kategori-menu">
        <i class="mdi mdi-book menu-icon"></i>
        <span class="menu-title">Data Kategori</span>
        <i class="menu-arrow"></i>
    </a>
    <div class="collapse" id="kategori-menu">
        <ul class="nav flex-column sub-menu">
            <li class="nav-item {{ Request::is('admin/kategori/tambah') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('admin/kategori/tambah') }}">Tambah Kategori</a>
            </li>
            <li class="nav-item {{ Request::is('admin/kategori/daftar') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('admin/kategori/daftar') }}">Daftar Kategori</a>
            </li>
        </ul>
    </div>
</li>
                -->


                {{-- <li
                    class="nav-item {{ Request::is('admin/laporanpembelian') || Request::is('admin/laporanpenjualan') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#laporan" aria-expanded="false"
                        aria-controls="laporan">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Laporan</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="laporan">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item {{ Request::is('admin/laporanpembelian') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('admin/laporanpembelian') }}">Laporan
                                    Produksi</a></li>
                            <li class="nav-item {{ Request::is('admin/laporanpenjualan') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('admin/laporanpenjualan') }}">Laporan
                                    Penjualan</a></li>
                        </ul>
                    </div>
                </li> --}}
                <!-- <li class="nav-item">
            <a class="nav-link" href="penggunadaftar.php">
              <i class="mdi mdi-emoticon menu-icon"></i>
              <span class="menu-title">Data Pengguna</span>
            </a>
          </li> -->
                <li
                    class="nav-item {{ Request::is('admin/internaltambah') || Request::is('admin/internaldaftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="mdi mdi-account menu-icon"></i>
                        <span class="menu-title">Data Internal</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="auth">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item {{ Request::is('admin/internaltambah') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('admin/internaltambah') }}">
                                    Tambah Internal </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/internaldaftar') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('admin/internaldaftar') }}">Daftar Internal</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- OWNER -->

                <?php } ?>
                <?php if (auth()->user()->role == 'Owner') { ?>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#produksiOwner" aria-expanded="false" aria-controls="produksiOwner">
                            <i class="mdi mdi-factory menu-icon"></i>
                            <span class="menu-title">Data Barang Produksi</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="produksiOwner">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('owner/produksidaftar') }}">Daftar Produksi</a>
                                </li>
                                {{-- <li class="nav-item">
                                    <a class="nav-link" href="{{ url('owner/produksireview') }}">Review Produksi</a>
                                </li> --}}
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item {{ Request::is('owner/showroomdaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#showroom-menu" aria-expanded="false" aria-controls="showroom-menu">
                                <i class="mdi mdi-store menu-icon"></i>
                                <span class="menu-title">Barang Masuk Showroom</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse {{ Request::is('owner/showroomdaftar') ? 'show' : '' }}" id="showroom-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item {{ Request::is('owner/showroomdaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('owner/showroomdaftar') }}">Daftar Barang Showroom</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ Request::is('owner/penjualandaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#penjualan" aria-expanded="false" aria-controls="penjualan">
                                <i class="mdi mdi-cart menu-icon"></i>
                                <span class="menu-title">Transaksi Penjualan</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="penjualan">
                                <ul class="nav flex-column sub-menu">
                                    {{-- <li class="nav-item {{ Request::is('kasir/penjualantambah') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('kasir/penjualantambah') }}">Tambah Penjualan</a>
                                    </li> --}}
                                    <li class="nav-item {{ Request::is('owner/penjualandaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('owner/penjualandaftar') }}">Daftar Penjualan</a>
                                    </li>
                                </ul>
                            </div>
                        </li>


                        <li class="nav-item {{ Request::is('owner/stokopname*') ? 'active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#stokopname" aria-expanded="false" aria-controls="stokopname">
                                    <i class="mdi mdi-clipboard-check-outline menu-icon"></i>
                                    <span class="menu-title">Stok Opname</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="collapse {{ Request::is('owner/stokopname*') ? 'show' : '' }}" id="stokopname">
                                    <ul class="nav flex-column sub-menu">
                                        {{-- <li class="nav-item">
                                            <a class="nav-link {{ Request::is('owner/stokopname') ? 'active' : '' }}" href="{{ route('stokopname.daftar') }}">
                                                Daftar Barang
                                            </a>
                                        </li>
                                        --}}
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('owner/stokopname/riwayat') ? 'active' : '' }}" href="{{ route('stokopname.riwayat') }}">
                                                Riwayat Opname
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                                    
                <!-- <li class="nav-item">
            <a class="nav-link" href="pembelian.php">
              <i class="mdi mdi-view-headline menu-icon"></i>
              <span class="menu-title">Transaksi</span>
            </a>
          </li> -->
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#beli" aria-expanded="false" aria-controls="beli">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Barang Masuk</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="beli">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ url('owner/barangmasukdaftar') }}">Daftar
                                    Barang
                                    Masuk</a></li>
                        </ul>
                    </div>
                </li> --}}

                {{--<li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#jual" aria-expanded="false" aria-controls="jual">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Barang Keluar</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="jual">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a
                                    class="nav-link {{ Request::is('owner/barangkeluardaftar') ? 'active' : '' }}"
                                    href="{{ url('owner/barangkeluardaftar') }}">Daftar Barang
                                    Keluar</a></li>
                        </ul>
                    </div>
                </li> --}}

                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#stok" aria-expanded="false" aria-controls="stok">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Stock Opname</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="stok">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ url('owner/stockopnamedaftar') }}">Daftar
                                    Stock
                                    Opname</a></li>
                        </ul>
                    </div>
                </li> --}}
                {{-- <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#laporan" aria-expanded="false"
                        aria-controls="laporan">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Laporan</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="laporan">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="{{ url('owner/laporanpembelian') }}">Laporan Produksi</a></li>
                            <li class="nav-item"> <a class="nav-link" href="{{ url('owner/laporanpenjualan') }}">Laporan Penjualan</a></li>
                        </ul>
                    </div>
                </li> --}}
                <!-- <li class="nav-item">
            <a class="nav-link" href="penggunadaftar.php">
              <i class="mdi mdi-emoticon menu-icon"></i>
              <span class="menu-title">Data Pengguna</span>
            </a>
          </li> -->
                <li class="nav-item {{ Request::is('owner/internaldaftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#auth" aria-expanded="false" aria-controls="auth">
                        <i class="mdi mdi-account menu-icon"></i>
                        <span class="menu-title">Data Internal</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="auth">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item {{ Request::is('owner/internaldaftar') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('owner/internaldaftar') }}">Daftar Internal</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- KASIR -->

                <?php } ?>
                <?php if (auth()->user()->role == 'Kasir') { ?>

                    <li class="nav-item {{ Request::is('kasir/showroomdaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#showroom-menu" aria-expanded="false" aria-controls="showroom-menu">
                                <i class="mdi mdi-store menu-icon"></i>
                                <span class="menu-title">Barang Masuk Showroom</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse {{ Request::is('kasir/showroomdaftar') ? 'show' : '' }}" id="showroom-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item {{ Request::is('kasir/showroomdaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('kasir/showroomdaftar') }}">Daftar Barang Showroom</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
        
                        <li class="nav-item {{ Request::is('kasir/penjualantambah') || Request::is('kasir/penjualandaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#penjualan" aria-expanded="false" aria-controls="penjualan">
                                <i class="mdi mdi-cart menu-icon"></i>
                                <span class="menu-title">Transaksi Penjualan</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="penjualan">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item {{ Request::is('kasir/penjualantambah') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('kasir/penjualantambah') }}">Tambah Penjualan</a>
                                    </li>
                                    <li class="nav-item {{ Request::is('kasir/penjualandaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('kasir/penjualandaftar') }}">Daftar Penjualan</a>
                                    </li>
                                </ul>
                            </div>
                        </li>



                <!-- GUDANG -->

                <?php } ?>
                @if (auth()->user()->role == 'Gudang')

                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#produksi" aria-expanded="false" aria-controls="produksi">
                        <i class="mdi mdi-factory menu-icon"></i>
                        <span class="menu-title">Data Barang Produksi</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="produksi">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('gudang/produksitambah') }}">Tambah Produksi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('gudang/produksidaftar') }}">Daftar Produksi</a>
                            </li>         
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ Request::is('gudang/showroomdaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#showroom-menu" aria-expanded="false" aria-controls="showroom-menu">
                                <i class="mdi mdi-store menu-icon"></i>
                                <span class="menu-title">Barang Masuk Showroom</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse {{ Request::is('gudang/showroomdaftar') ? 'show' : '' }}" id="showroom-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item {{ Request::is('gudang/showroomdaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('gudang/showroomdaftar') }}">Daftar Barang Showroom</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ Request::is('gudang/penjualandaftar') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#penjualan" aria-expanded="false" aria-controls="penjualan">
                                <i class="mdi mdi-cart menu-icon"></i>
                                <span class="menu-title">Transaksi Penjualan</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="penjualan">
                                <ul class="nav flex-column sub-menu">
                                    {{-- <li class="nav-item {{ Request::is('kasir/penjualantambah') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('kasir/penjualantambah') }}">Tambah Penjualan</a>
                                    </li> --}}
                                    <li class="nav-item {{ Request::is('gudang/penjualandaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ url('gudang/penjualandaftar') }}">Daftar Penjualan</a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ Request::is('gudang/stokopname*') ? 'active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#stokopname" aria-expanded="false" aria-controls="stokopname">
                                    <i class="mdi mdi-clipboard-check-outline menu-icon"></i>
                                    <span class="menu-title">Stok Opname</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="collapse {{ Request::is('gudang/stokopname*') ? 'show' : '' }}" id="stokopname">
                                    <ul class="nav flex-column sub-menu">
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('gudang/stokopname') ? 'active' : '' }}" href="{{ route('stokopname.daftar') }}">
                                                Daftar Barang
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('gudang/stokopname/riwayat') ? 'active' : '' }}" href="{{ route('stokopname.riwayat') }}">
                                                Riwayat Opname
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item {{ Request::is('gudang/laporan/*') ? 'active' : '' }}">
                                <a class="nav-link" data-toggle="collapse" href="#laporanGudang" aria-expanded="false" aria-controls="laporanGudang">
                                    <i class="mdi mdi-file-document-box menu-icon"></i>
                                    <span class="menu-title">Laporan Gudang</span>
                                    <i class="menu-arrow"></i>
                                </a>
                                <div class="collapse {{ Request::is('gudang/laporan/*') ? 'show' : '' }}" id="laporanGudang">
                                    <ul class="nav flex-column sub-menu">
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('gudang/laporan/produksi') ? 'active' : '' }}" href="{{ url('gudang/laporan/produksi') }}">Laporan Barang Produksi</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('gudang/laporan/stokopname') ? 'active' : '' }}" href="{{ url('gudang/laporan/stokopname') }}">Laporan Stok Opname</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>



                        {{-- <li class="nav-item {{ Request::is('gudang/stokopnamedaftar*') || Request::is('gudang/stokopnamedaftarhasil*') ? 'active' : '' }}">
                            <a class="nav-link" data-toggle="collapse" href="#stokOpname" aria-expanded="false" aria-controls="stokOpname">
                                <i class="mdi mdi-clipboard-check menu-icon"></i>
                                <span class="menu-title">Stok Opname</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="stokOpname">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item {{ Request::is('gudang/stokopnamedaftar') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('gudang.stokopnamedaftar') }}">Tambah Stok Opname</a>
                                    </li>
                                    <li class="nav-item {{ Request::is('gudang/stokopnamedaftarhasil') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('gudang.stokopnamedaftarhasil') }}">Daftar Stok Opname</a>
                                    </li>
                                </ul>
                            </div>
                        </li> --}}




                {{-- <li class="nav-item {{ Request::is('gudang/stockopnamedaftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#stok" aria-expanded="false" aria-controls="stok">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Stock Opname</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="stok">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item {{ Request::is('gudang/stockopnamedaftar') ? 'active' : '' }}"> <a
                                    class="nav-link" href="{{ url('gudang/stockopnamedaftar') }}">Daftar Stock
                                    Opname</a></li>
                        </ul>
                    </div>
                </li> --}}


                {{--<li class="nav-item {{ Request::is('gudang/barangkeluardaftar') ? 'active' : '' }}">
                    <a class="nav-link" data-toggle="collapse" href="#jual" aria-expanded="false" aria-controls="jual">
                        <i class="mdi mdi-book menu-icon"></i>
                        <span class="menu-title">Data Barang Keluar</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="jual">
                        <ul class="nav flex-column sub-menu">
                            <!-- <li class="nav-item"> <a class="nav-link" href="{{ url('gudang/barangkeluartambah') }}">Tambah Barang Keluar</a></li> -->
                            <li class="nav-item {{ Request::is('gudang/barangkeluardaftar') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ url('gudang/barangkeluardaftar') }}">Daftar Barang
                                    Keluar</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                @endif
            </ul>
        </nav>

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:./partials/_navbar.html -->
            <nav class="navbar col-lg-12 col-12 px-0 py-0 py-lg-4 d-flex flex-row">
                <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button"
                        data-toggle="minimize">
                        <span class="mdi mdi-menu"></span>
                    </button>
                    <div class="navbar-brand-wrapper">
                        <input type="file" id="profileFileInput" style="display: none;" accept="image/*">
                        <a class="navbar-brand brand-logo" id="profileEdit" href="javascript:void(0);">
                            @if ($file = auth()->user()->file)
                            <img src="{{ asset('storage/' . $file) }}" width="70" alt="logo" />
                            @else
                            <img src="{{ asset('assets/admin/assets_admin/adm.png') }}" width="70" alt="logo" />
                            @endif
                        </a>
                        <a class="navbar-brand brand-logo-mini" href="javascript:void(0);"><img
                                src="{{ asset('assets/admin/assets_admin/adm.png') }}" width="70" alt="logo" /></a>
                    </div>
                    <h4 class="font-weight-bold mb-0 d-none d-md-block mt-1">
                        <?= auth()->user()->name ?>
                    </h4>
                    <ul class="navbar-nav navbar-nav-right">
                        <li class="nav-item nav-profile dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                                <span class="nav-profile-name">
                                    <?= auth()->user()->name ?>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                                aria-labelledby="profileDropdown">
                                @php
                                if (auth()->user()->role == 'Admin') {
                                $url = 'admin/profile';
                                } elseif (auth()->user()->role == 'Kasir') {
                                $url = 'kasir/profile';
                                } elseif (auth()->user()->role == 'Owner') {
                                $url = 'owner/profile';
                                } elseif (auth()->user()->role == 'Gudang') {
                                $url = 'gudang/profile';
                                }
                                @endphp
                                <a class="dropdown-item" href="{{ url($url) }}">
                                    <i class="mdi mdi-settings text-primary"></i>
                                    Ubah Profil
                                </a>
                                <a class="dropdown-item" href="{{ url('logout') }}"
                                    onclick="return confirm('Apakah Anda Yakin Ingin Keluar')">
                                    <i class="mdi mdi-logout text-primary"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                        data-toggle="offcanvas">
                        <span class="mdi mdi-menu"></span>
                    </button>
                </div>

            </nav>

            <div class="main-panel">
                <div>
                    @yield('content')

                    {{--
                    <footer class="footer">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-sm-flex justify-content-center justify-content-sm-between">
                                    <span
                                        class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright
                                        ©
                                        bootstrapdash.com 2020</span>
                                    <span
                                        class="text-muted d-block text-center text-sm-left d-sm-inline-block">Distributed
                                        By:
                                        <a href="https://www.themewagon.com/" target="_blank">ThemeWagon</a></span>
                                    <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"> Free <a
                                            href="https://www.bootstrapdash.com/" target="_blank">Bootstrap dashboard
                                            templates</a> from Bootstrapdash.com</span>
                                </div>
                            </div>
                        </div>
                    </footer>
                    --}}
                </div>
            </div>
        </div>

        <script src="{{ url('/') }}/assets/admin/assets_admin/vendors/js/vendor.bundle.base.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets_admin/vendors/chart.js/Chart.min.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets_admin/js/off-canvas.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets_admin/js/hoverable-collapse.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets_admin/js/template.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets_admin/js/dashboard.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js">
        </script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/DataTables-1.10.18/js/dataTables.bootstrap4.min.js">
        </script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/JSZip-2.5.0/jszip.min.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/pdfmake-0.1.36/pdfmake.min.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/pdfmake-0.1.36/vfs_fonts.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/Buttons-1.5.6/js/buttons.html5.min.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/Buttons-1.5.6/js/buttons.print.min.js"></script>
        <script src="{{ url('/') }}/assets/admin/assets/DataTables/Buttons-1.5.6/js/buttons.colvis.min.js"></script>


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

        <script>
            $(document).ready(function() {
                var table = $('#table').DataTable({
                    buttons: ['csv', 'print', 'excel', 'pdf'],
                    dom: "<'row'<'col-md-3'l><'col-md-5'B><'col-md-4'f>>" +
                        "<'row'<'col-md-12'tr>>" +
                        "<'row'<'col-md-5'i><'col-md-7'p>>",
                    lengthMenu: [
                        [5, 10, 25, 50, 100, -1],
                        [5, 10, 25, 50, 100, "ALL"]
                    ]
                });

                table.buttons().container()
                    .appendTo('#table_wrapper .col-md-5:eq(0)');
            });
        </script>
        <script>
            $(document).ready(function() {
                var table = $('#table2').DataTable({
                    buttons: ['csv', 'print', 'excel', 'pdf'],
                    dom: "<'row'<'col-md-3'l><'col-md-5'B><'col-md-4'f>>" +
                        "<'row'<'col-md-12'tr>>" +
                        "<'row'<'col-md-5'i><'col-md-7'p>>",
                    lengthMenu: [
                        [5, 10, 25, 50, 100, -1],
                        [5, 10, 25, 50, 100, "ALL"]
                    ]
                });

                table.buttons().container()
                    .appendTo('#table_wrapper .col-md-5:eq(0)');
            });
        </script>
        <script>
            document.getElementById('profileEdit').addEventListener('click', function() {
                document.getElementById('profileFileInput').click();
            });

            document.getElementById('profileFileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                fetch('/update-profile-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Upload gagal');
                        return response.json();
                    })
                    .then(data => {
                        alert('Foto berhasil diperbarui!');
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengunggah foto.');
                    });
            });
        </script>


        @yield('script')

</body>

</html>