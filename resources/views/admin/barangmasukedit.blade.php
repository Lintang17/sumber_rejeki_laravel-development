@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Barang Masuk</h4>

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ url('admin/barangmasukupdate/' . $notabeli) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Tanggal Barang Masuk</label>
                            <input type="date" name="tanggalpembelian" class="form-control" value="{{ $tanggalpembelian}}" required>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="barangTable">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pembeliandetail as $index => $barang)
                                        <tr>
                                            <td>
                                                <input type="text" name="namabarang[]" class="form-control" value="{{ $barang->namabarang }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="harga[]" class="form-control harga" value="{{ number_format($barang->harga, 0, '', '.') }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="jumlah[]" class="form-control jumlah" value="{{ $barang->jumlah }}" required>
                                            </td>
                                            <td>
                                                <input type="text" name="total[]" class="form-control total" value="{{ number_format($barang->total, 0, '', '.') }}" readonly required>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Grand Total -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <label><strong>Grand Total:</strong></label>
                                <input type="text" id="grandtotal" name="grandtotalnon" class="form-control bg-light" 
                                    value="{{ number_format($pembeliandetail->first()->grandtotal, 0, '', '.') }}" readonly>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="row mt-4">
                            <div class="col-md-12 d-flex justify-content-start">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="mdi mdi-content-save"></i> Simpan
                                </button>
                                <a href="{{ url('admin/barangmasukdaftar') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-close-circle-outline"></i> Batal
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Recalculate total and grand total
    function updateTotals() {
        let grandTotal = 0;
        document.querySelectorAll('#barangTable tbody tr').forEach(row => {
            let harga = parseInt(row.querySelector('.harga').value.replace(/\./g, '') || 0);
            let jumlah = parseInt(row.querySelector('.jumlah').value || 0);
            let total = harga * jumlah;
            row.querySelector('.total').value = formatRupiah(total);
            grandTotal += total;
        });
        document.getElementById('grandtotal').value = formatRupiah(grandTotal);
    }

    // Format angka ke format rupiah
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Event listener untuk input harga dan jumlah
    document.querySelectorAll('.harga, .jumlah').forEach(input => {
        input.addEventListener('input', updateTotals);
    });

    // Hapus baris
    document.querySelectorAll('.remove').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('tr').remove();
            updateTotals();
        });
    });
</script>
@endsection
