@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Produksi Barang</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('gudang/produksisimpan') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="tanggalproduksi">Tanggal Produksi</label>
                            <input type="date" name="tanggalproduksi" class="form-control"
                                value="{{ old('tanggalproduksi') }}" required
                                oninvalid="this.setCustomValidity('Harap isi Tanggal Produksi')" 
                                oninput="this.setCustomValidity('')">
                        </div>

                        <div class="form-group">
                            <label for="namaproduk">Nama Produk</label>
                            <input type="text" name="namaproduk" class="form-control"
                                value="{{ old('namaproduk') }}" required
                                oninvalid="this.setCustomValidity('Harap isi Nama Produk')" 
                                oninput="this.setCustomValidity('')">
                        </div>

                        <div class="form-group">
                            <label for="stok">Stok Awal Produksi</label>
                            <input type="number" name="stok" class="form-control" min="1"
                                value="{{ old('stok') }}" required
                                oninvalid="this.setCustomValidity('Harap isi Stok Awal Produksi')" 
                                oninput="this.setCustomValidity('')">
                            <small class="text-muted">Ini adalah stok awal untuk produk baru.</small>
                        </div>

                        <div class="form-group">
                            <label for="hppestimasi">HPP Estimasi</label>
                            <input type="text" id="hppestimasi_display" class="form-control"
                                value="{{ old('hppestimasi') ? number_format(old('hppestimasi'), 0, ',', '.') : '' }}"
                                required
                                oninvalid="this.setCustomValidity('Harap isi HPP Estimasi')" 
                                oninput="this.setCustomValidity('')">
                            <input type="hidden" name="hppestimasi" id="hppestimasi"
                                value="{{ old('hppestimasi') }}">
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi Produk</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required
                                oninvalid="this.setCustomValidity('Harap isi Deskripsi Produk')" 
                                oninput="this.setCustomValidity('')">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="fotoproduk">Foto Produk</label>
                            <input type="file" name="fotoproduk" class="form-control-file" id="fotoprodukInput"
                                accept="image/*" required
                                oninvalid="this.setCustomValidity('Harap unggah Foto Produk')" 
                                oninput="this.setCustomValidity('')">
                            <small class="form-text text-muted">Maksimal 2MB. Format jpg, jpeg, png.</small>

                            <div class="mt-2">
                                <img id="preview" src="#" alt="Preview" style="display: none; max-width: 200px;" />
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan</button>
                        <a href="{{ url('gudang/produksidaftar') }}" class="btn btn-light">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Format angka ke ribuan --}}
<script>
    const displayInput = document.getElementById('hppestimasi_display');
    const hiddenInput = document.getElementById('hppestimasi');

    displayInput.addEventListener('input', function () {
        let value = this.value.replace(/[^\d]/g, '');
        hiddenInput.value = value;
        this.value = new Intl.NumberFormat('id-ID').format(value);
    });
</script>

{{-- Preview Foto --}}
<script>
    const fileInput = document.getElementById('fotoprodukInput');
    const previewImage = document.getElementById('preview');

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            previewImage.src = URL.createObjectURL(file);
            previewImage.style.display = 'block';
        } else {
            previewImage.src = '#';
            previewImage.style.display = 'none';
        }
    });
</script>
@endsection
