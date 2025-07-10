@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Produksi Barang</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('gudang/produksiupdate/' . $produksi->idproduksi) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="tanggalproduksi">Tanggal Produksi</label>
                            <input type="date" class="form-control" value="{{ $produksi->tanggalproduksi }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="namaproduk">Nama Produk</label>
                            <input type="text" name="namaproduk" class="form-control"
                                value="{{ old('namaproduk', $produksi->namaproduk) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="jumlah">Stok Produksi</label>
                            <div class="mb-2">
                                <strong>Stok Awal:</strong> {{ $produksi->stok_awal ?? 0 }}<br>
                                <strong>Stok Tambahan:</strong> {{ $produksi->stok_tambahan ?? 0 }}<br>
                                <strong>Total:</strong> {{ $produksi->stok }}
                            </div>
                            <input type="number" class="form-control" value="{{ $produksi->stok }}" readonly>
                            <small class="text-muted">Stok tidak bisa diubah. Untuk menambah stok gunakan fitur <strong>Tambah Stok Produksi</strong>.</small>
                        </div>

                        <div class="form-group">
                            <label for="hppestimasi">HPP Estimasi</label>
                            <input type="text" id="hppestimasi_display" class="form-control"
                                value="{{ number_format(old('hppestimasi', $produksi->hppestimasi), 0, ',', '.') }}" required>
                            <input type="hidden" name="hppestimasi" id="hppestimasi"
                                value="{{ old('hppestimasi', $produksi->hppestimasi) }}">
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi Produk</label>
                            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $produksi->deskripsiproduk) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="fotoproduk">Foto Produk (Opsional)</label><br>
                            @if($produksi->fotoproduk)
                                <p>Foto Lama:</p>
                                <img src="{{ asset('uploads/foto_produk/' . $produksi->fotoproduk) }}" width="120" id="previewOld"><br><br>
                            @endif
                            <input type="file" name="fotoproduk" class="form-control-file" id="fotoprodukInput" accept="image/*">
                            <img id="previewNew" src="#" style="display:none; max-width: 200px; margin-top: 10px;">
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ url('gudang/produksidaftar') }}" class="btn btn-light">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Format HPP Estimasi --}}
<script>
    const displayInput = document.getElementById('hppestimasi_display');
    const hiddenInput = document.getElementById('hppestimasi');

    displayInput.addEventListener('input', function () {
        let value = this.value.replace(/[^\d]/g, '');
        hiddenInput.value = value;
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });
</script>

{{-- Preview Foto Baru --}}
<script>
    const fileInput = document.getElementById('fotoprodukInput');
    const previewNew = document.getElementById('previewNew');

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            previewNew.src = URL.createObjectURL(file);
            previewNew.style.display = 'block';
        } else {
            previewNew.style.display = 'none';
        }
    });
</script>
@endsection
