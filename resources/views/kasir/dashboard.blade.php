@extends('layouts.admin')

@section('content')
<style>
    .card {
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.05);
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

    .btn-light {
        border-radius: 50px;
        font-weight: 600;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
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
                
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #3E3232;">
                    <div class="card-body">
                        <h5 class="count-number count-uang" data-target="{{ $totalpenjualan }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Pemasukan Bulan Ini</p>
                        <a href="{{ url('kasir/penjualandaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #A7727D;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $jumlahbarangshowroom }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Barang Showroom</p>
                        <a href="{{ url('kasir/showroomdaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #3E3232;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $jumlahpenjualan }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Transaksi Penjualan</p>
                        <a href="{{ url('kasir/penjualandaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
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
