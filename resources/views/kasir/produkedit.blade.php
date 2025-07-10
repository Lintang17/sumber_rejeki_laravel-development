{{--@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Ubah Barang</h4>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form class="forms-sample" method="post" action="{{ url('kasir/produkupdate', $produk->idproduk) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="namaproduk">Nama Produk</label>
                                <input type="text" value="{{ $produk->namaproduk }}" class="form-control"
                                    name="namaproduk" required>
                            </div>

                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" value="{{ $produk->stok }}" class="form-control" name="stok"
                                    required>
                            </div>

                            <div class="form-group">
                                <label for="hargajual">Harga Jual</label>
                                <input type="number" value="{{ $produk->hargajual }}" class="form-control" name="hargajual"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>Foto Produk</label><br>
                                <img src="{{ asset('assets/foto/' . $produk->fotoproduk) }}" width="200">
                            </div>

                            <div class="form-group">
                                <label for="foto">Ganti Foto</label>
                                <input type="file" class="form-control" name="foto" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <a href="{{ url('kasir/produkdaftar') }}" class="btn btn-light">Batal</a>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
-- }}