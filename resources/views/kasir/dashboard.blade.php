@extends('layouts.admin')

@section('content')
<style>
    .card {
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }
    .card-body {
        padding-top: 3rem;
        padding-bottom: 3rem;
        text-align: center;
    }
    .count-number {
        font-weight: 700;
        font-family: 'Poppins', sans-serif;
        color: #ffffff !important;
        text-shadow:
            0 0 5px rgba(255, 255, 255, 0.8),
            0 0 10px rgba(255, 255, 255, 0.6),
            0 0 15px rgba(0, 0, 0, 0.7);
    }
    .count-produk {
        font-size: 3.2rem;
    }
    .count-uang {
        font-size: 3rem;
    }
    .text-white-50{
        color:rgba(255,255,255,.85)!important;
    }
    .badge-status{
        padding:8px 15px;
        border-radius:30px;
        font-size:12px;
        font-weight:700;
        color:#fff !important;
    }
    .notification-icon{
        width:50px;
        height:50px;
        border-radius:14px;
        background:#F8EFE9;
        display:flex;
        align-items:center;
        justify-content:center;
        margin-right:15px;
    }
    .notification-icon i{
        font-size:24px;
        color:#A7727D;
    }
    .penjualan-card{
        background:#fff;
        border-radius:20px;
        box-shadow:0 8px 25px rgba(0,0,0,.08);
        border:1px solid #790000;
    }
    .penjualan-title{
        display:flex;
        align-items:center;
    }
    .penjualan-heading{
        font-size:18px;
        font-weight:700;
        color:#333;
    }
    .penjualan-count{
        background:#A7727D;
        color:#fff;
        padding:8px 18px;
        border-radius:30px;
        font-size:13px;
        font-weight:600;
        margin-right:10px;
    }
    .penjualan-item{
        background:#fff8f5;
        border:1px solid #f1dfd8;
        border-radius:12px;
        padding:16px 20px;
        margin-bottom:12px;
        transition:.3s;
        color:#333;
    }
    .penjualan-item:hover{
        background:#fdf2ee;
        text-decoration:none;
        color:#333;
    }
    .penjualan-code{
        font-weight:700;
        color:#5d4037;
    }
    .penjualan-date{
        color:#666;
        font-size:14px;
        font-weight:700; 
        text-align:left;
        margin-top:4px;
        display:block;
    }
    .badge-status{
        padding:8px 15px;
        border-radius:30px;
        font-size:12px;
        font-weight:700;
    }
    .empty-penjualan{
        text-align:center;
        padding:45px 0;
    }
    .empty-icon{
        font-size:55px;
        color:#ccc;
        margin-bottom:10px;
    }
    .stats-card{
        border-radius:18px;
        overflow:hidden;
        position:relative;
        height:100%;
        box-shadow:0 6px 20px rgba(0,0,0,.08);
    }
    .stats-card::before{
        content:"";
        position:absolute;
        right:-35px;
        top:-35px;
        width:120px;
        height:120px;
        border-radius:50%;
        background:rgba(255,255,255,.08);
    }
    .stats-card::after{
        content:"";
        position:absolute;
        left:-40px;
        bottom:-40px;
        width:120px;
        height:120px;
        border-radius:50%;
        background:rgba(255,255,255,.05);
    }
    .stats-card .card-body{
        display:flex;
        flex-direction:column;
        justify-content:space-between;
        min-height:150px;
        padding:15px;
        text-align:left;
    }
    .dashboard-icon{
        width:58px;
        height:58px;
        border-radius:16px;
        background:rgba(255,255,255,.18);
        display:flex;
        align-items:center;
        justify-content:center;
        margin-bottom:20px;
    }
    .dashboard-icon i{
        color:#fff;
        font-size:28px;
    }
    .dashboard-title{
        color:rgba(255,255,255,.92);
        font-size:15px;
        margin-bottom:15px;
    }
    .btn-light{
        border-radius:30px;
        font-weight:600;
        width:fit-content;
    }
    
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm"
                    style="background:linear-gradient(135deg,#A7727D,#8B5E5E); border-radius:20px;">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex justify-content-start align-items-center">
                            <div class="text-left">
                                <h3 class="text-white font-weight-bold mb-1">
                                    Dashboard Kasir
                                </h3>
                                <p class="mb-0 text-white-50">
                                    Selamat datang di Sistem Informasi UD Sumber Rejeki.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm penjualan-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap pb-3 mb-4"
                    style="border-bottom:1px solid #710c0c;">
                    <h5 class="penjualan-title mb-0">
                        <span class="notification-icon">
                            <i class="mdi mdi-bell-ring"></i>
                        </span>
                        <div>
                            <div class="penjualan-heading">
                                Transaksi Penjualan Terbaru
                            </div>
                        </div>
                    </h5>
                    <div class="d-flex align-items-center">
                        <span class="penjualan-count">
                            {{ $penjualanTerbaru->count() }} Transaksi
                        </span>
                        <a href="{{ url('kasir/penjualandaftar') }}"
                            class="btn btn-sm btn-light">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                @if($penjualanTerbaru->count() > 0)
                    @foreach($penjualanTerbaru as $penjualan)
                        <a href="{{ url('kasir/penjualandaftar') }}"
                            class="penjualan-item d-flex justify-content-between align-items-center text-decoration-none">
                            <div class="text-left">
                                <div class="penjualan-code">
                                    {{ $penjualan->kodenota }}
                                    @if($penjualan->namapembeli)
                                        - {{ $penjualan->namapembeli }}
                                    @endif
                                </div>
                                <div class="penjualan-date">
                                    {{ \Carbon\Carbon::parse($penjualan->tanggalpenjualan)->format('d M Y') }}
                                </div>
                            </div>
                            <span class="badge-status bg-success text-white">
                                Rp {{ number_format($penjualan->grandtotal,0,',','.') }}
                            </span>
                        </a>
                    @endforeach
                @else
                <div class="empty-penjualan">
                    <i class="mdi mdi-bell-off-outline empty-icon"></i>
                    <h6>Belum Ada Transaksi Penjualan Terbaru</h6>
                    <p>Transaksi penjualan terbaru akan muncul di sini.</p>
                </div>
                @endif
            </div>
        </div>

        <div class="row">

            {{-- <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #6B4F4F;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $jumlahproduk }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Data Produk</p>
                        <a href="{{ url('admin/barangmasukdaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div> --}}

            {{-- <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #A7727D;">
                    <div class="card-body">
                        <h5 class="count-number count-uang" data-target="{{ $totalpembelian }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Pengeluaran Bulan Ini</p>
                        <a href="{{ url('admin/barangmasukdaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
                --}}
                
            <div class="col-lg-4 col-md-6 grid-margin stretch-card">
                <div class="card stats-card"
                    style="background:linear-gradient(135deg,#4E342E,#2E1F1B);">
                    <div class="card-body">
                        <div class="dashboard-icon">
                            <i class="mdi mdi-cash-multiple"></i>
                        </div>
                        <div class="count-number count-uang"
                            data-target="{{ $totalpenjualan }}">
                            0
                        </div>
                        <div class="dashboard-title">
                            Pemasukan Bulan Ini
                        </div>
                        <a href="{{ url('kasir/penjualandaftar') }}"
                            class="btn btn-light">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 grid-margin stretch-card">
                <div class="card stats-card"
                    style="background:linear-gradient(135deg,#8D6E63,#5D4037);">
                    <div class="card-body">
                        <div class="dashboard-icon">
                            <i class="mdi mdi-sofa"></i>
                        </div>
                        <div class="count-number count-produk"
                            data-target="{{ $jumlahbarangshowroom }}">
                            0
                        </div>
                        <div class="dashboard-title">
                            Barang Showroom
                        </div>
                        <a href="{{ url('kasir/showroomdaftar') }}"
                            class="btn btn-light">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 grid-margin stretch-card">
                <div class="card stats-card"
                    style="background:linear-gradient(135deg,#C48B9F,#8E5A6A);">
                    <div class="card-body">
                        <div class="dashboard-icon">
                           <i class="mdi mdi-cart-outline"></i>
                        </div>
                        <div class="count-number count-produk"
                            data-target="{{ $jumlahpenjualan }}">
                            0
                        </div>
                        <div class="dashboard-title">
                            Transaksi Penjualan
                        </div>
                        <a href="{{ url('kasir/penjualandaftar') }}"
                            class="btn btn-light">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.count-number');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                let count = +counter.innerText.replace(/[^0-9]/g, '');

                const increment = target / 200;

                if (count < target) {
                    count = Math.ceil(count + increment);
                    if (counter.classList.contains('count-uang')) {
                        counter.innerText = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                        }).format(count);
                    } else {
                        counter.innerText = count;
                    }
                    setTimeout(updateCount, 10);
                } else {
                    if (counter.classList.contains('count-uang')) {
                        counter.innerText = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0,
                        }).format(target);
                    } else {
                        counter.innerText = target;
                    }
                }
            };
            updateCount();
        });
    });
</script>
@endsection
