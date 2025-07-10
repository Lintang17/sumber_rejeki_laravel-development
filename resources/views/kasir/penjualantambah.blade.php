@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Penjualan</h4>
                    <form class="forms-sample" method="post" action="{{ url('kasir/penjualansimpan') }}">
                        @csrf

                        <div class="form-group">
                            <label>Nama Pembeli</label>
                            <input class="form-control" name="namapembeli" type="text" placeholder="Nama Pembeli" required>
                        </div>

                        <div class="form-group">
                            <label>No Telepon</label>
                            <input class="form-control" name="notelp" type="text" placeholder="08xxxxxxxxxx" required>
                        </div>

                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat" placeholder="Alamat Pembeli" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Penjualan</label>
                            <input class="form-control" name="tanggalpenjualan" type="date" required value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="form-group">
                            <label>Scan Barcode</label>
                            <input type="text" id="barcodeInput" class="form-control" placeholder="Scan Barcode" autofocus autocomplete="off">
                        </div>

                        <table class="table table-bordered table-striped" id="tabelform">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="idproduk[]" class="form-control produkselect" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produk as $item)
                                                @php
                                                     $stokSisa = ($item->stok_awal + $item->stok_tambahan) - $item->stok_terjual;
                                                @endphp
                                                <option value="{{ $item->idshowroom }}"
                                                    data-nama="{{ $item->produksi->namaproduk }}"
                                                    data-harga="{{ $item->produksi->hargajual }}"
                                                    data-stok="{{ $stokSisa }}"
                                                    data-barcode="{{ $item->barcode }}">
                                                    {{ $item->produksi->namaproduk }} - {{ $stokSisa }} pcs
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control stoktersedia" readonly></td>
                                    <td><input type="text" name="harga[]" class="form-control harga" readonly></td>
                                    <td><input type="number" name="jumlahpembelian[]" class="form-control jumlah" min="1" value="1" required></td>
                                    <td><input type="text" name="total[]" class="form-control total" readonly></td>
                                    <td><button type="button" class="btn btn-success add-row">+</button></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="form-group">
                            <label>Sub Total</label>
                            <input class="form-control" id="subtotal" type="text" readonly>
                        </div>

                        <div class="form-group">
                            <label>Grand Total</label>
                            <input class="form-control" id="grandtotal_display" type="text" readonly>
                            <input type="hidden" id="grandtotal" name="grandtotal" readonly required>
                        </div>

                        <div class="form-group">
                            <label>DP (Down Payment)</label>
                            <input class="form-control" id="dp" name="dp" type="text" placeholder="Misal: 1.000.000">
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select class="form-control" name="metodepembayaran" required>
                                <option value="Tunai">Tunai</option>
                                <option value="QRIS">QRIS</option>
                                <option value="Transfer">Transfer</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Uang Pembeli</label>
                            <input class="form-control" id="uangpembeli" name="bayar" type="text" required placeholder="Misal: 2.000.000">
                        </div>

                        <div class="form-group">
                            <label>Kembalian</label>
                            <input class="form-control" id="kembalian" name="kembali" type="text" readonly>
                        </div>

                        <div class="form-group">
                            <label>Sisa Pembayaran</label>
                            <input class="form-control" id="sisabayar" name="sisabayar" type="text" readonly>
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ url('kasir/dashboard') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formatRibuan = angka => angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    const parseNumber = str => parseFloat(str.replace(/\./g, '')) || 0;

    const dpInput = document.getElementById('dp');
    const bayarInput = document.getElementById('uangpembeli');
    const sisaField = document.getElementById('sisabayar');
    const kembaliField = document.getElementById('kembalian');

    function updateTotal() {
        let subtotal = 0;
        document.querySelectorAll('.total').forEach(el => subtotal += parseNumber(el.value));
        document.getElementById('subtotal').value = formatRibuan(subtotal);
        document.getElementById('grandtotal_display').value = formatRibuan(subtotal);
        document.getElementById('grandtotal').value = subtotal;

        const dp = parseNumber(dpInput.value);
        const bayar = parseNumber(bayarInput.value);

        if (dp > 0) {
            sisaField.value = formatRibuan(Math.max(subtotal - dp, 0));
            kembaliField.value = formatRibuan(Math.max(bayar - dp, 0));
        } else {
            sisaField.value = '';
            kembaliField.value = formatRibuan(Math.max(bayar - subtotal, 0));
        }
    }

    function validateInputs() {
        const dp = parseNumber(dpInput.value);
        const bayar = parseNumber(bayarInput.value);
        const subtotal = parseNumber(document.getElementById('grandtotal').value);

        if (dp > 0 && bayar > 0 && bayar < dp) {
            alert("Uang pembeli tidak boleh kurang dari DP");
        } else if (dp === 0 && bayar > 0 && bayar < subtotal) {
            alert("Uang pembeli tidak boleh kurang dari Grand Total");
        }
    }

    ['dp', 'uangpembeli'].forEach(id => {
        const input = document.getElementById(id);

        // Hanya format saat ngetik
        input.addEventListener('input', function () {
            const val = this.value.replace(/\D/g, '');
            this.value = formatRibuan(val);
            updateTotal();
        });

        // Validasi hanya saat user selesai input (pindah fokus)
        input.addEventListener('blur', function () {
            validateInputs();
        });

        input.addEventListener('focus', function () {
            if (this.value === '0') this.value = '';
        });
    });

    // Event: pilih produk
    document.querySelector('#tabelform tbody').addEventListener('change', function (e) {
        if (e.target.classList.contains('produkselect')) {
            const row = e.target.closest('tr');
            const selected = e.target.selectedOptions[0];

            const stok = selected.dataset.stok ?? 0;
            const harga = selected.dataset.harga ?? 0;

            row.querySelector('input.stoktersedia').value = stok;
            row.querySelector('.harga').value = formatRibuan(harga);
            
            const jumlah = row.querySelector('.jumlah').value || 1;
            row.querySelector('.total').value = formatRibuan(parseNumber(selected.dataset.harga) * jumlah);
            updateTotal();
        }
    });

    // Event: input jumlah produk
    document.querySelector('#tabelform tbody').addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah')) {
            const row = e.target.closest('tr');
            const harga = parseNumber(row.querySelector('.harga').value);
            const jumlah = parseInt(e.target.value) || 0;
            const stok = parseInt(row.querySelector('.stoktersedia').value) || 0;

            if (jumlah > stok) {
                alert('Jumlah melebihi stok tersedia');
                e.target.value = stok;
            }

            row.querySelector('.total').value = formatRibuan(harga * jumlah);
            updateTotal();
        }
    });

    document.querySelector('#tabelform').addEventListener('click', function(e) {
    if (e.target.classList.contains('add-row')) {
        const row = e.target.closest('tr');
        const clone = row.cloneNode(true);
        clone.querySelectorAll('input').forEach(input => {
            input.value = '';
        });
        clone.querySelector('.btn').classList.remove('btn-success', 'add-row');
        clone.querySelector('.btn').classList.add('btn-danger', 'remove-row');
        clone.querySelector('.btn').innerText = '-';
        row.parentNode.appendChild(clone);
    } else if (e.target.classList.contains('remove-row')) {
        const row = e.target.closest('tr');
        row.remove();
        updateTotal();
    }
});

    // Scan barcode
    document.getElementById('barcodeInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const barcode = this.value.trim();
            document.querySelectorAll('.produkselect').forEach(select => {
                for (let opt of select.options) {
                    if (opt.dataset.barcode === barcode) {
                        select.value = opt.value;
                        select.dispatchEvent(new Event('change'));
                        break;
                    }
                }
            });
            this.value = '';
        }
    });
});
</script>
@endsection
