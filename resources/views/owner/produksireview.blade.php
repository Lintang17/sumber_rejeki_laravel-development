@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-10 offset-md-1 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        {{ $produksi->status == 'Selesai' ? 'Edit Produksi Selesai' : 'Edit Produksi' }}
                    </h4>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif
                    @if ($produksi->status == 'Selesai')
                        <div class="alert alert-info">
                            Produksi sudah selesai. Hanya Nama Produk, HPP Final,
                            Harga Jual, Foto, dan Deskripsi yang bisa diedit.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            Produksi belum selesai. Semua kolom bisa diedit.
                        </div>
                    @endif

                    <form action="{{ url('owner/produksireview/' . $produksi->idproduksi) }}"
                          method="POST"
                          enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" name="namaproduk"
                                   class="form-control"
                                   value="{{ old('namaproduk', $produksi->namaproduk) }}"
                                   required>
                        </div>
                        @if ($produksi->status != 'Selesai')
                            <div class="form-group">
                                <label>Status Produksi</label>
                                <select name="status" class="form-control">
                                    <option value="Menunggu"
                                        {{ old('status', $produksi->status) == 'Menunggu' ? 'selected' : '' }}>
                                        Menunggu
                                    </option>
                                    <option value="Selesai"
                                        {{ old('status', $produksi->status) == 'Selesai' ? 'selected' : '' }}>
                                        Selesai
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Produksi</label>
                                <input type="date" name="tanggalproduksi"
                                       class="form-control"
                                       value="{{ old('tanggalproduksi', $produksi->tanggalproduksi) }}">
                            </div>
                            <div class="form-group">
                                <label>Stok Awal</label>
                                <input type="number" name="stok_awal"
                                       class="form-control" min="0"
                                       value="{{ old('stok_awal', $produksi->stok_awal) }}">
                            </div>
                            <div class="form-group">
                                <label>Stok Tambahan</label>
                                <input type="number" name="stok_tambahan"
                                       class="form-control" min="0"
                                       value="{{ old('stok_tambahan', $produksi->stok_tambahan) }}">
                            </div>
                            <div class="form-group">
                                <label>Stok Total</label>
                                <input type="number" name="stok"
                                       class="form-control" min="0"
                                       value="{{ old('stok', $produksi->stok) }}">
                            </div>
                            <div class="form-group">
                                <label>HPP Estimasi</label>
                                <input type="text"
                                        id="hppestimasi"
                                        name="hppestimasi"
                                        class="form-control"
                                        inputmode="numeric"
                                        value="{{ old('hppestimasi', $produksi->hppestimasi !== null ? number_format($produksi->hppestimasi, 0, ',', '.') : '') }}">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="datetime-local" name="tanggalselesai"
                                       class="form-control"
                                       value="{{ old('tanggalselesai', $produksi->tanggalselesai ? \Carbon\Carbon::parse($produksi->tanggalselesai)->format('Y-m-d\TH:i') : '') }}">
                            </div>
                        @else
                            <div class="form-group">
                                <label>Status Produksi</label>
                                <input type="text" class="form-control"
                                       value="Selesai" readonly>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Produksi</label>
                                <input type="text" class="form-control"
                                       value="{{ $produksi->tanggalproduksi ? \Carbon\Carbon::parse($produksi->tanggalproduksi)->format('d-m-Y') : '-' }}"
                                       readonly>
                            </div>
                            <div class="form-group">
                                <label>Stok Awal</label>
                                <input type="text" class="form-control"
                                       value="{{ $produksi->stok_awal ?? 0 }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Stok Tambahan</label>
                                <input type="text" class="form-control"
                                       value="{{ $produksi->stok_tambahan ?? 0 }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Stok Total</label>
                                <input type="text" class="form-control"
                                       value="{{ $produksi->stok ?? 0 }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>HPP Estimasi</label>
                                <input type="text" class="form-control"
                                       value="{{ number_format($produksi->hppestimasi ?? 0, 0, ',', '.') }}"
                                       readonly>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Selesai</label>
                                <input type="text" class="form-control"
                                       value="{{ $produksi->tanggalselesai ? \Carbon\Carbon::parse($produksi->tanggalselesai)->format('d-m-Y H:i') : '-' }}"
                                       readonly>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>HPP Final</label>
                            <input type="text"
                                    id="hppfinal"
                                    name="hppfinal"
                                    class="form-control"
                                    inputmode="numeric"
                                    value="{{ old('hppfinal', $produksi->hppfinal !== null ? number_format($produksi->hppfinal, 0, ',', '.') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>Harga Jual</label>
                            <input type="text"
                                    id="hargajual"
                                    name="hargajual"
                                    class="form-control"
                                    inputmode="numeric"
                                    value="{{ old('hargajual', $produksi->hargajual ? number_format($produksi->hargajual, 0, ',', '.') : '') }}"
                                    {{ $produksi->status == 'Selesai' ? 'required' : '' }}>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Update</label>
                            <input type="text" class="form-control"
                                   value="{{ $produksi->tanggal_update ? \Carbon\Carbon::parse($produksi->tanggal_update)->format('d-m-Y H:i') : '-' }}"
                                   readonly>
                            <small class="text-muted">
                                Diperbarui otomatis saat menyimpan.
                            </small>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi Produk</label>
                            <textarea name="deskripsiproduk"
                                      class="form-control"
                                      rows="4">{{ old('deskripsiproduk', $produksi->deskripsiproduk) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Foto Produk</label>
                            <div class="mb-2">
                                <img
                                    id="previewFoto"
                                    src="{{ $produksi->fotoproduk
                                        ? asset('uploads/foto_produk/' . $produksi->fotoproduk)
                                        : '' }}"
                                    class="img-thumbnail"
                                    style="max-width:200px; {{ $produksi->fotoproduk ? '' : 'display:none;' }}"
                                    alt="Preview Foto Produk">
                            </div>
                            <input type="file"
                                    id="fotoproduk"
                                    name="fotoproduk"
                                    class="form-control"
                                    accept=".jpg,.jpeg,.png,.webp">
                            <small class="text-muted">
                                Format JPG, JPEG, PNG, WEBP. Maksimal 5 MB.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-success">
                            Simpan Perubahan
                        </button>
                        <a href="{{ url('owner/produksidaftar') }}"
                           class="btn btn-light">
                            Kembali
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hppInput = document.getElementById('hppfinal');
    const jualInput = document.getElementById('hargajual');
    const form = document.querySelector('form');

    // Format Rupiah
    function formatRupiah(value) {
        let angka = value.replace(/\D/g, '');
        if (!angka) {
            return '';
        }
        return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    [hppInput, jualInput].forEach(function (input) {
        if (!input) return;
        input.value = formatRupiah(input.value);
        input.addEventListener('input', function () {
            this.value = formatRupiah(this.value);
        });
    });

    // Preview Foto Produk
    const inputFoto = document.getElementById('fotoproduk');
    const previewFoto = document.getElementById('previewFoto');
    inputFoto.addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {
                alert('Format foto harus JPG, JPEG, PNG, atau WEBP.');
                this.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran foto maksimal 5 MB.');
                this.value = '';
                return;
            }

            previewFoto.src = URL.createObjectURL(file);
            previewFoto.style.display = 'block';
        }
    });

    form.addEventListener('submit', function () {
        [hppInput, jualInput].forEach(function (input) {
            if (input) {
                input.value = input.value.replace(/\./g, '');
            }
        });
    });
});
</script>

@endsection