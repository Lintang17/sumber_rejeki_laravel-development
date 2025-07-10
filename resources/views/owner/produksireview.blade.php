@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-10 offset-md-1 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Review Produksi Barang</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('owner/produksireview/' . $produksi->idproduksi) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" class="form-control" value="{{ $produksi->namaproduk }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Produksi</label>
                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($produksi->tanggalproduksi)->format('d-m-Y') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input type="text" class="form-control" value="{{ $produksi->stok_awal ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Stok Tambahan</label>
                            <input type="text" class="form-control" value="{{ $produksi->stok_tambahan ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Stok Total</label>
                            <input type="text" class="form-control" value="{{ $produksi->stok }}" readonly>
                        </div>

                        @if ($produksi->stok_tambahan > 0)
                            <div class="alert alert-warning">
                                <strong>Perhatian:</strong> Produksi ini memiliki stok tambahan dan memerlukan review ulang.
                            </div>
                        @endif

                        <div class="form-group">
                            <label>HPP Estimasi</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($produksi->hppestimasi, 0, ',', '.') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="hppfinal">HPP Final</label>
                            <input type="text" id="hppfinal" name="hppfinal" class="form-control" 
                                value="{{ old('hppfinal', number_format($produksi->hppfinal ?? 0, 0, ',', '.')) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="hargajual">Harga Jual</label>
                            <input type="text" id="hargajual" name="hargajual" class="form-control"
                                value="{{ old('hargajual', number_format($produksi->hargajual ?? 0, 0, ',', '.')) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi Produk</label>
                            <textarea class="form-control" rows="3" readonly>{{ $produksi->deskripsiproduk }}</textarea>
                        </div>

                        @if($produksi->fotoproduk)
                            <div class="form-group">
                                <label>Foto Produk</label><br>
                                <img src="{{ asset('uploads/foto_produk/' . $produksi->fotoproduk) }}" 
                                    alt="Foto Produk" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        @endif

                        <button type="submit" class="btn btn-success">Simpan Review</button>
                        <a href="{{ url('owner/produksidaftar') }}" class="btn btn-light">Kembali</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script Format Harga --}}
<script>
    const hppInput = document.getElementById('hppfinal');
    const jualInput = document.getElementById('hargajual');

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID').format(value);
    }

    function cleanNumber(str) {
        return str.replace(/[^\d]/g, '');
    }

    [hppInput, jualInput].forEach(input => {
        input.addEventListener('input', function () {
            let value = cleanNumber(this.value);
            if (value) {
                this.value = formatRupiah(value);
            } else {
                this.value = '';
            }
        });

        input.addEventListener('focus', function () {
            if (cleanNumber(this.value) === '0') {
                this.value = '';
            }
        });
    });

    document.querySelector('form').addEventListener('submit', function () {
        hppInput.value = cleanNumber(hppInput.value);
        jualInput.value = cleanNumber(jualInput.value);
    });
</script>
@endsection
