@extends('layouts.admin')

@section('content')
<style>
    .card {
        border-radius: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
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

        <div class="card border-0 shadow-sm mb-4" style="background:#1f2937;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold" style="color:#ffffff;">
                        Notifikasi PO Terbaru
                    </h5>

                    <span class="px-3 py-2 fw-bold" style="background:#374151;color:#ffffff;border-radius:8px;">
                        {{ $poBaru ? $poBaru->count() : 0 }} PO
                    </span>
                </div>

                @if($poBaru && $poBaru->count() > 0)

                    <div style="border:1px solid #374151;border-radius:10px;overflow:hidden;">
                        @foreach($poBaru as $po)

                            <a href="{{ url('admin/po') }}"
                                class="d-flex justify-content-between align-items-center text-decoration-none"
                                style="padding:14px 16px;border-bottom:1px solid #374151;background:#111827;">
                                
                                <div>
                                    <div style="font-weight:800;color:#ffffff;font-size:15px;">
                                        {{ $po->kode_po }} - {{ $po->customer }}
                                    </div>

                                    <div style="font-size:12px;color:#d1d5db;">
                                        {{ $po->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>

                                <span style="
                                    @if($po->status == 'Pending')
                                        background:#facc15;color:#111827;
                                    @elseif($po->status == 'Disetujui')
                                        background:#3b82f6;color:#ffffff;
                                    @elseif($po->status == 'Diproses')
                                        background:#06b6d4;color:#ffffff;
                                    @elseif($po->status == 'Selesai')
                                        background:#22c55e;color:#ffffff;
                                    @else
                                        background:#ef4444;color:#ffffff;
                                    @endif
                                    font-weight:900;
                                    padding:6px 14px;
                                    border-radius:8px;
                                    font-size:12px;">
    
                                    {{ strtoupper($po->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else

                    <div style="text-align:center;padding:16px;color:#d1d5db;border:1px solid #374151;border-radius:10px;">
                        Tidak ada data PO terbaru
                    </div>
                @endif
            </div>
        </div>

        <div class="row">

        <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #6B4F4F;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $jumlahbarangshowroom }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Barang Showroom</p>
                        <a href="{{ url('admin/showroomdaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #A7727D;">
                    <div class="card-body">
                        <h5 class="count-number count-produk" data-target="{{ $jumlahproduk }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Data Barang Produksi</p>
                        <a href="{{ url('admin/produksidaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 grid-margin stretch-card">
                <div class="card" style="background-color: #3E3232;">
                    <div class="card-body">
                        <h5 class="count-number count-uang" data-target="{{ $totalpenjualan }}">0</h5>
                        <p class="text-white mb-0" style="font-size: 16px;">Pemasukan Bulan Ini</p>
                        <a href="{{ url('admin/penjualandaftar') }}" class="btn btn-light btn-sm mt-3">Baca Selengkapnya</a>
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