@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row justify-content-center">
        <div class="col-md-8 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title">Tambah Stok Opname</h4>
                    <form action="{{ route('stokopname.simpan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="showroom_id" value="{{ $showroom->idshowroom }}">

                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" class="form-control" value="{{ $showroom->produksi->namaproduk ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Opname</label>
                            <input type="date" name="tanggal_opname" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Stok Sistem (sisa)</label>
                            <input type="number" class="form-control" value="{{ $showroom->stok_sisa }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Stok Fisik</label>
                            <input type="number" name="stok_fisik" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan Opname</button>
                        <a href="{{ route('stokopname.daftar') }}" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
