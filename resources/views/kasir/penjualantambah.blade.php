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
                                    <th>Discount</th>
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
                                            <!-- @foreach ($produk as $item)
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
                                            @endforeach -->
                                            @foreach ($produk as $item)
                                                @if ($item->produksi)
                                                    @php
                                                        $stokSisa = ($item->stok_awal + $item->stok_tambahan) - $item->stok_terjual;
                                                    @endphp

                                                    <option value="{{ $item->idshowroom }}"
                                                        data-nama="{{ $item->produksi->namaproduk }}"
                                                        data-harga="{{ $item->produksi->hargajual }}"
                                                        data-stok="{{ $stokSisa }}"
                                                        data-discount="{{ $item->discount ?? 0 }}"
                                                        data-barcode="{{ $item->barcode }}">
                                                        {{ $item->produksi->namaproduk }} - {{ $stokSisa }} pcs
                                                    </option>
                                                @endif
                                            @endforeach

                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control stoktersedia" readonly></td>
                                    <td><input type="text" name="harga[]" class="form-control harga" readonly></td>
                                    <td><input type="text" class="form-control discount" readonly disabled></td>
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
                            <label>Total Diskon</label>
                            <input class="form-control" id="total_diskon_display" type="text" readonly>
                            <input type="hidden" id="total_diskon" name="total_diskon">
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
        let totalDiskon = 0;

        document.querySelectorAll('#tabelform tbody tr').forEach(row => {
            const select = row.querySelector('.produkselect');
            const selected = select.selectedOptions[0];

            const harga = parseNumber(row.querySelector('.harga').value);
            const jumlah = parseInt(row.querySelector('.jumlah').value) || 0;
            const discount = parseFloat(selected?.dataset.discount) || 0;

            // Harga seluruh barang sebelum diskon
            const subtotalBaris = harga * jumlah;

            // Diskon per unit dikalikan jumlah barang
            const diskonPerUnit = Math.round(harga * discount / 100);
            const diskonBaris = diskonPerUnit * jumlah;

            // Total baris setelah diskon
            const totalBaris = Math.max(0, subtotalBaris - diskonBaris);

            row.querySelector('.total').value = formatRibuan(totalBaris);

            subtotal += subtotalBaris;
            totalDiskon += diskonBaris;
        });

        const grandtotal = Math.max(0, subtotal - totalDiskon);

        document.getElementById('subtotal').value = formatRibuan(subtotal);
        document.getElementById('total_diskon_display').value = formatRibuan(totalDiskon);
        document.getElementById('total_diskon').value = totalDiskon;

        document.getElementById('grandtotal_display').value = formatRibuan(grandtotal);
        document.getElementById('grandtotal').value = grandtotal;

        const dp = parseNumber(dpInput.value);
        const bayar = parseNumber(bayarInput.value);

        sisaField.value = dp > 0
            ? formatRibuan(Math.max(grandtotal - dp, 0))
            : '';

        // Jika memakai DP, kembalian dihitung dari uang yang diterima
        // dikurangi DP yang dibayarkan saat ini.
        kembaliField.value = formatRibuan(
            Math.max(bayar - (dp > 0 ? dp : grandtotal), 0)
        );
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
            const discount = parseFloat(selected.dataset.discount) || 0;

            row.querySelector('input.stoktersedia').value = stok;
            row.querySelector('.harga').value = formatRibuan(harga);

            // Tampilkan discount otomatis dari showroom
            const discountInput = row.querySelector('.discount');

            if (discount > 0) {
                discountInput.value = discount + ' %';
                discountInput.disabled = false;
            } else {
                discountInput.value = '';
                discountInput.disabled = true;
            }

            updateTotal();
        }
    });

   // Event: input jumlah produk
    document.querySelector('#tabelform tbody').addEventListener('input', function (e) {
        if (e.target.classList.contains('jumlah')) {
            const row = e.target.closest('tr');
            const stok = parseInt(row.querySelector('.stoktersedia').value) || 0;
            let jumlah = parseInt(e.target.value) || 0;

            if (jumlah > stok) {
                alert('Jumlah melebihi stok tersedia');
                jumlah = stok;
                e.target.value = stok;
            }

            if (jumlah < 0) {
                jumlah = 0;
                e.target.value = 0;
            }

            updateTotal();
        }
    });
            

    document.querySelector('#tabelform').addEventListener('click', function(e) {
    if (e.target.classList.contains('add-row')) {
        const row = e.target.closest('tr');
        const clone = row.cloneNode(true);

        clone.querySelector('.produkselect').selectedIndex = 0;
        clone.querySelector('.stoktersedia').value = '';
        clone.querySelector('.harga').value = '';
        clone.querySelector('.discount').value = '';
        clone.querySelector('.discount').disabled = true;
        clone.querySelector('.jumlah').value = 1;
        clone.querySelector('.total').value = '';

        clone.querySelector('.btn').classList.remove('btn-success', 'add-row');
        clone.querySelector('.btn').classList.add('btn-danger', 'remove-row');
        clone.querySelector('.btn').innerText = '-';
        row.parentNode.appendChild(clone);
        updateTotal();
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
