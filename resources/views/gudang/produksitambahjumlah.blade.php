@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-8 offset-md-2 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Tambah Stok Produksi</h4>

                    <div class="mb-3">
                        <strong>Nama Produk:</strong> {{ $produksi->namaproduk }}<br>
                        <strong>Status:</strong> 
                        <span class="badge 
                            @if($produksi->status == 'Selesai') badge-primary
                            @elseif($produksi->status == 'Menunggu') badge-warning
                            @else badge-secondary @endif">
                            {{ $produksi->status }}
                        </span>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('gudang/produksitambahjumlah/' . $produksi->idproduksi) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $produksi->stok_awal ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Stok Tambahan Sebelumnya</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $produksi->stok_tambahan ?? 0 }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Stok Total Saat Ini</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $produksi->stok }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="stok_tambahan">Stok Tambahan Baru <span class="text-danger">*</span></label>
                            <input type="number" name="stok_tambahan" class="form-control" min="1" required placeholder="Masukkan stok tambahan">
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Stok
                            </button>
                            <a href="{{ url('gudang/produksidaftar') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-group label {
        font-weight: 500;
    }
</style>
@endpush
