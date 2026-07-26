@extends('layouts.admin')

@section('content')
<style>
    body {
        background: #f4f6fb;
    }
    .card {
        border: 0;
        border-radius: 18px;
        overflow: hidden;
        transition: .3s;
        box-shadow: 0 6px 20px rgba(0,0,0,.08);
    }
    .card:hover{
        transform: translateY(-4px);
        box-shadow:0 18px 40px rgba(0,0,0,.15);
    }
    .content-wrapper{
        padding-top:0px;
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
    .stats-card{
        border-radius:18px;
        overflow:hidden;
        position:relative;
        height:100%;
    }
    .stats-card .card-body{
        display:flex;
        flex-direction:column;
        justify-content:space-between;
        min-height:150px;
        padding:15px;
    }
    .stats-card .btn{
        margin-top:auto;
        width:fit-content;
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
    .count-number{
        color:white;
        font-size:3rem;
        font-weight:700;
        margin-bottom:10px;
        line-height:1;
        text-shadow:0 5px 12px rgba(0,0,0,.18);
    }
    .dashboard-title{
        color:rgba(255,255,255,.92);
        font-size:15px;
        margin-bottom:15px;
    }
    .grid-margin{
        margin-bottom:25px;
    }
    .stats-card:hover .dashboard-icon{
        transform:rotate(-8deg) scale(1.08);
    }
    .stats-card:hover .btn-light{
        background:white;
    }
    .btn-light i{
        margin-right:4px;
    }
    .count-produk{
        margin-bottom:12px;
    }
    .count-uang{
        margin-bottom:12px;
        word-break:break-word;
    }
    .btn-light{
        border-radius:30px;
        font-weight:600;
        padding:8px 20px;
    }
    .notification-icon{
        width:50px;
        height:50px;
        border-radius:15px;
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
    .po-title{
        display:flex;
        align-items:center;
    }
    .po-heading{
        color:#333;
        font-size:18px;
        font-weight:800 !important;
        letter-spacing:.3px;
    }
    .po-card{
        background:#fff;
        border-radius:20px;
        box-shadow:0 8px 25px rgba(0,0,0,.08);
        border:1px solid #790000;
    }
    .po-count{
        background:#A7727D;
        color:white;
        border-radius:30px;
        padding:8px 18px;
        margin-right:10px;
        font-size:13px;
    }
    .po-item:last-child{
        margin-bottom:0;
    }
    .po-item{
        background:#FFF8F3;
        border:1px solid #E9D5C5;
        color:#333;
        margin-bottom:12px;
        padding:16px 20px;
        border-radius:14px;
        transition:.3s;
    }
    .po-item:hover{
        background:#FDF1E8;
    }
    .po-code{
        color:#5A3D2B;
        font-weight:700;
        font-size:15px;
    }
    .po-date{
        color:#8A6B55;
        font-weight:700;
        font-size:15px;
    }
    .badge-status{
        padding:8px 16px;
        border-radius:25px;
        font-size:11px;
        font-weight:700;
        letter-spacing:.5px;
        box-shadow:0 4px 10px rgba(0,0,0,.18);
    }
    .empty-po{
        padding:60px 20px;
        text-align:center;
        color:#9ca3af;
    }
    .empty-icon{
        font-size:65px;
        color:#d1d5db;
        margin-bottom:15px;
    }
    .empty-po h6{
        font-weight:700;
        color:#555;
    }
    .empty-po p{
        margin-bottom:0;
    }
    .dashboard-header-icon{
        width:65px;
        height:65px;
        border-radius:18px;
        background:rgba(255,255,255,.18);
        display:flex;
        justify-content:center;
        align-items:center;
    }
    .dashboard-header-icon i{
        color:#fff;
        font-size:32px;
    }
    .text-white-50{
        color:rgba(255,255,255,.8)!important;
    }
    
</style>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm"
                    style="background:linear-gradient(135deg,#A7727D,#8B5E5E); border-radius:20px;">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center flex-wrap">
                            <div class="ml-3">
                                <h3 class="text-white font-weight-bold mb-1">
                                    Dashboard Admin
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

        <div class="card shadow-sm po-card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap pb-3 mb-4"
                    style="border-bottom:1px solid #710c0c;">
                    <h5 class="po-title mb-0">
                        <span class="notification-icon">
                            <i class="mdi mdi-bell-ring"></i>
                        </span>

                        <div>
                            <div class="po-heading">
                                Notifikasi PO Terbaru
                            </div>
                        </div>
                    </h5>

                    <div class="d-flex align-items-center">
                        <span class="po-count">
                            {{ $poBaru->count() }} PO
                        </span>

                        <a href="{{ url('admin/po') }}"
                            class="btn btn-sm btn-light"
                            style="font-weight:600;">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                @if($poBaru && $poBaru->count() > 0)

                    <div class="mt-4">
                        @foreach($poBaru as $po)

                            <a href="{{ url('admin/po') }}"
                                class="po-item d-flex justify-content-between align-items-center text-decoration-none">                            
                                <div>
                                    <div class="po-code">
                                        {{ $po->kode_po }} - {{ $po->customer }}
                                    </div>

                                    <div class="po-date">
                                        {{ $po->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>

                                <span class="badge-status"
                                    style="
                                    @if($po->status == 'Pending')
                                        background:#facc15;color:#000;
                                    @elseif($po->status == 'Disetujui')
                                        background:#3b82f6;color:#fff;
                                    @elseif($po->status == 'Diproses')
                                        background:#06b6d4;color:#fff;
                                    @elseif($po->status == 'Diambil')
                                        background:#8b5cf6;color:#fff;
                                    @elseif($po->status == 'Dikirim')
                                        background:#f97316;color:#fff;
                                    @elseif($po->status == 'Selesai')
                                        background:#22c55e;color:#fff;
                                    @else
                                        background:#ef4444;color:#fff;
                                    @endif">
                                        {{ strtoupper($po->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else

                <div class="empty-po">
                    <i class="mdi mdi-bell-off-outline empty-icon"></i>
                    <h6>Belum Ada Purchase Order</h6>
                    <p>
                        Semua Purchase Order terbaru akan muncul di sini.
                    </p>
                </div>
                @endif
            </div>
        </div>

        <div class="row">

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
                    <a href="{{ url('admin/showroomdaftar') }}"
                        class="btn btn-light">
                        <i class="mdi mdi-arrow-right-circle-outline"></i>
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
                        <i class="mdi mdi-factory"></i>
                    </div>
                    <div class="count-number count-produk"
                        data-target="{{ $jumlahproduk }}">
                        0
                    </div>
                    <div class="dashboard-title">
                        Data Barang Produksi
                    </div>
                    <a href="{{ url('admin/produksidaftar') }}"
                        class="btn btn-light">
                        <i class="mdi mdi-arrow-right-circle-outline"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
            
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
                    <a href="{{ url('admin/penjualandaftar') }}"
                        class="btn btn-light">
                        <i class="mdi mdi-arrow-right-circle-outline"></i>
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>

            {{-- Pengeluaran Hari Ini 
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card" style="background-color: #C49292;">
                    <div class="card-body">
                        <h5 class="count-number count-uang" data-target="{{ $totalpembelianhariini }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Pengeluaran Hari Ini</p>
                        <a href="{{ url('admin/barangmasukdaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div> --}}

            {{-- Pemasukan Hari Ini 
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card" style="background-color: #614040;">
                    <div class="card-body">
                        <h5 class="count-number count-uang" data-target="{{ $totalpenjualanhariini }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Pemasukan Hari Ini</p>
                        <a href="{{ url('admin/barangkeluardaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div> --}}

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