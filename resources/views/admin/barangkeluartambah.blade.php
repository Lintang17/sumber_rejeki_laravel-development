@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Penjualan</h4>
                        <form class="forms-sample" method="post" action="{{ url('admin/barangkeluarsimpan') }}">
                            @csrf

                            <div class="form-group">
                                <label>Tanggal Penjualan</label>
                                <input class="form-control" name="tanggalpenjualan" type="date" required
                                    value="{{ date('Y-m-d') }}">
                            </div> 

                            <table class="table table-bordered table-striped" id="tabelform">
                                <thead>
                                    <tr>
                                        <th width="30%">Nama Produk</th>
                                        <th>Harga</th>
                                        <th width="15%">Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="form-group namabarangharga">
                                                <select name="namabarang[]" class="form-control namabarang" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($produk as $product)
                                                        <option value="{{ $product->namaproduk }}"
                                                            data-price="{{ $product->hargajual }}"
                                                            data-stock="{{ $product->stok }}">
                                                            {{ $product->namaproduk }} (Stok: {{ $product->stok }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control harga" name="harga[]"
                                                    value="0" readonly required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control jumlah" name="jumlah[]"
                                                    value="1" min="1" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control total" name="total[]"
                                                    value="0" readonly>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success add-row">+</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="form-group">
                                <label>Diskon (%)</label>
                                <input class="form-control" id="diskon" name="diskon" type="number" min="0"
                                    max="100" value="0" step="0.1">
                            </div>

                            <div class="form-group">
                                <label>Sub Total</label>
                                <input class="form-control" id="subtotal" name="subtotal" type="number" readonly>
                            </div>

                            <div class="form-group">
                                <label>Grand Total</label>
                                <input class="form-control" id="grandtotal" name="grandtotal" type="number" readonly>
                                <input type="hidden" id="grandtotalnon" name="grandtotalnon" readonly required>
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
                                <input class="form-control" id="uangpembeli" name="uangpembeli" type="number"
                                    min="0">
                            </div>

                            <div class="form-group">
                                <label>Kembalian</label>
                                <input class="form-control" id="kembalian" name="kembalian" type="number" readonly>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#tabelform tbody');
            const diskonInput = document.getElementById('diskon');
            const uangPembeliInput = document.getElementById('uangpembeli');

            // Initial calculation
            calculateAllTotals();

            // Setup event delegation for the table
            tableBody.addEventListener('click', function(e) {
                // Handle add row button
                if (e.target.classList.contains('add-row')) {
                    addNewRow();
                }

                // Handle remove row button
                if (e.target.classList.contains('remove-row')) {
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        e.target.closest('tr').remove();
                        calculateAllTotals();
                    }
                }
            });

            // Setup event delegation for product selection changes
            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('namabarang')) {
                    const row = e.target.closest('tr');
                    const selected = e.target.options[e.target.selectedIndex];
                    row.querySelector('.harga').value = selected.dataset.price || '0';
                    calculateRowTotal(row);
                    calculateAllTotals();
                }
            });

            // Setup event delegation for quantity changes
            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('jumlah')) {
                    const row = e.target.closest('tr');
                    calculateRowTotal(row);
                    calculateAllTotals();
                }
            });

            // Setup discount and payment event listeners
            diskonInput.addEventListener('input', calculateAllTotals);
            uangPembeliInput.addEventListener('input', calculateChange);

            // Function to add a new row
            function addNewRow() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <div class="form-group namabarangharga">
                            <select name="namabarang[]" class="form-control namabarang" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($produk as $product)
                                    <option value="{{ $product->namaproduk }}"
                                        data-price="{{ $product->hargajual }}"
                                        data-stock="{{ $product->stok }}">
                                        {{ $product->namaproduk }} (Stok: {{ $product->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control harga" name="harga[]" value="0"
                                readonly required>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control jumlah" name="jumlah[]" value="1"
                                min="1" required>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="number" class="form-control total" name="total[]" value="0"
                                readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <button type="button" class="btn btn-danger remove-row">-</button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(newRow);
            }

            // Calculate the total for a single row
            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.jumlah').value) || 0;
                const price = parseFloat(row.querySelector('.harga').value) || 0;
                const total = quantity * price;
                row.querySelector('.total').value = total;
            }

            // Calculate all totals (subtotal, discount, grand total)
            function calculateAllTotals() {
                let subtotal = 0;

                // Sum all row totals
                document.querySelectorAll('.total').forEach(totalInput => {
                    subtotal += parseFloat(totalInput.value) || 0;
                });

                // Update subtotal
                document.getElementById('subtotal').value = subtotal;

                // Calculate discount
                const discountPercent = parseFloat(diskonInput.value) || 0;
                const discountAmount = (subtotal * discountPercent) / 100;

                // Calculate grand total
                const grandTotal = subtotal - discountAmount;

                // Update grand total fields
                document.getElementById('grandtotal').value = grandTotal;
                document.getElementById('grandtotalnon').value = grandTotal;

                // Recalculate change
                calculateChange();
            }

            // Calculate change
            function calculateChange() {
                const grandTotal = parseFloat(document.getElementById('grandtotal').value) || 0;
                const payment = parseFloat(document.getElementById('uangpembeli').value) || 0;
                const change = payment - grandTotal;

                document.getElementById('kembalian').value = change >= 0 ? change : 0;
            }
        });
    </script>
@endsection
