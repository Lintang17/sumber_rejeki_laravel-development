@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="font-weight-bold">Laporan Produksi Barang</h3>
                        {{-- <h5 class="text-muted mb-1">UD Sumber Rejeki</h5>
                        <p class="mb-0">Jl. Gajah Mada No.197, Rambipuji, Jember</p> --}}
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('laporan.gudang.produksi.pdf') }}" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
                        </a>
                        <a href="{{ route('laporan.gudang.produksi.excel') }}" class="btn btn-success">
                            <i class="bi bi-file-earmark-excel-fill"></i> Download Excel
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Tambahan</th>
                                    <th>Stok Total</th>
                                    <th>HPP Final</th>
                                    <th>Harga Jual</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->namaproduk }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y') }}</td>
                                        <td>{{ $item->stok_awal }}</td>
                                        <td>{{ $item->stok_tambahan }}</td>
                                        <td>{{ $item->stok }}</td>
                                        <td>{{ $item->hppfinal ?? '-' }}</td>
                                        <td>{{ $item->hargajual ?? '-' }}</td>
                                        <td>{{ ucfirst($item->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
