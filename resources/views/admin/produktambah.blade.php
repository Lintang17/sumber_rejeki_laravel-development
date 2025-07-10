@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Barang</h4>
                        <form class="forms-sample" action="{{ url('admin/produksimpan') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="namaproduk">Nama Produk</label>
                                <select name="namaproduk" id="namaproduk" class="form-control" required>
                                    <option value="" selected disabled>Pilih Produk</option>
                                    @foreach ($barangmasuk as $key => $value)
                                        <option value="{{ $value->namabarang }}" data-jumlah="{{ $value->jumlah }}">
                                            {{ $value->namabarang }} - {{ $value->jumlah }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" class="form-control" name="stok" placeholder="Stok" readonly
                                    value="0">
                            </div>
                            <div class="form-group">
                                <label for="hargajual">Harga Jual</label>
                                <input id="harga" type="text" class="form-control" name="hargajual"
                                    placeholder="Harga Jual" required>
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto</label>
                                <input type="file" class="form-control" name="foto" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0"></script>
    <script>
        new AutoNumeric('#harga', {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
            unformatOnSubmit: true
        });

        document.addEventListener('DOMContentLoaded', function() {
            const selectProduk = document.getElementById('namaproduk');
            const inputStok = document.querySelector('input[name="stok"]');

            selectProduk.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const maxJumlah = selectedOption.dataset.jumlah || 0;

                inputStok.value = maxJumlah;
            });
        });
    </script>
@endsection
