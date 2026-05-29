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

    .btn-light {
        border-radius: 50px;
        font-weight: 600;
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">

        <div class="card border-0 mb-4" style="background:#1f2937;border-radius:12px;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-white mb-0">
                        Notifikasi PO Terbaru
                    </h5>

                    <span style="background:#374151;color:#fff;padding:6px 12px;border-radius:8px;font-weight:700;">
                        {{ isset($poBaru) ? $poBaru->count() : 0 }} PO
                    </span>
                </div>

                @if(isset($poBaru) && $poBaru->count() > 0)

                    <div style="border:1px solid #374151;border-radius:10px;overflow:hidden;">

                        @foreach($poBaru as $po)
                            <a href="{{ url('gudang/po') }}"
                                style="display:flex;justify-content:space-between;align-items:center;
                                    padding:14px 16px;
                                    border-bottom:1px solid #374151;
                                    background:#111827;
                                    text-decoration:none;">
                                <div>
                                    <div style="font-weight:800;color:#fff;">
                                        {{ $po->kode_po }} - {{ $po->customer }}
                                    </div>

                                    <div style="font-size:12px;color:#9ca3af;">
                                        {{ $po->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>

                                <span style="background:#facc15;color:#111827;
                                            font-weight:900;
                                            padding:6px 12px;
                                            border-radius:8px;">
                                    PENDING
                                </span>
                            </a>
                        @endforeach
                    </div>

                @else
                    <div style="padding:14px;text-align:center;color:#9ca3af;">
                        Tidak ada PO Terbaru
                    </div>
                @endif
            </div>
        </div>

        <div class="row">

        <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #6B4F4F;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $jumlahproduk }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Data Barang Produksi</p>
                        <a href="{{ url('gudang/produksidaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #A7727D;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $totalstokopname }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Stok Opname</p>
                        <a href="{{ url('gudang/stokopname/riwayat') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #3E3232;">
                    <div class="card-body">
                        <h5 class="count-number count-uang" data-target="{{ $totalpenjualan }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Pemasukan Bulan Ini</p>
                        <a href="{{ url('gudang/penjualandaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
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
