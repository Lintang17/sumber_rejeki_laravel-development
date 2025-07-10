@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tambah Barang Masuk</h4>
                        <form action="{{ url('admin/barangmasuksimpan') }}" class="forms-sample" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="">Tanggal Barang Masuk</label>
                                <input class="form-control" name="tanggalpembelian" type="date" required
                                    value="{{ old('tanggalpembelian', date('Y-m-d')) }}" autocomplete="off">
                            </div>
                            <br>
                            <table class="table table-bordered table-striped" id="tabelform">
                                <thead>
                                    <tr>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        {{-- <td width="30%">
                                            <select name="namabarang[]" class="form-control namabarang" required>
                                                <option value="">Pilih Produk</option>
                                                @foreach ($produk as $value)
                                                    <option value="{{ $value->namaproduk }}"
                                                        data-price="{{ $value->hargajual }}"
                                                        data-stok="{{ $value->stok }}">
                                                        {{ $value->namaproduk }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td> --}}
                                        <td width="30%">
                                            <input type="text" name="namabarang[]" class="form-control namabarang"
                                                required>
                                        </td>
                                        <td>
                                            <input type="text" name="harga[]" class="form-control harga" value="0"
                                                required>
                                        </td>
                                        <td>
                                            <input type="number" name="jumlah[]" class="form-control jumlah" value="0"
                                                min="1" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control totaldisplay" value="Rp 0" readonly>
                                            <input type="hidden" name="total[]" class="total" value="0">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success add-row">+</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label for="">Grand Total</label>
                                <input class="form-control" id="grandtotal" type="text" value="Rp 0" readonly>
                                <input class="form-control" id="grandtotalnon" name="grandtotalnon" type="hidden"
                                    value="0">
                            </div>
                            <button type="submit" class="btn btn-primary mr-2" name="simpan"
                                value="simpan">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Fungsi format angka jadi ribuan
            function formatRibuan(angka) {
                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            // Fungsi ambil angka dari string terformat ribuan
            function parseFormattedNumber(str) {
                return parseFloat(str.replace(/\./g, '')) || 0;
            }

            // Format input saat diketik
            function formatInputRibuan(input) {
                let value = input.value.replace(/[^0-9]/g, '');
                if (value === '') {
                    input.value = '';
                    return 0;
                }
                let angka = parseInt(value);
                input.value = formatRibuan(angka);
                return angka;
            }

            const tableBody = document.querySelector('#tabelform tbody');

            // Event delegation for add/remove row
            tableBody.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-row')) {
                    addNewRow();
                } else if (e.target.classList.contains('remove-row')) {
                    const row = e.target.closest('tr');
                    if (tableBody.rows.length > 1) {
                        row.remove();
                        calculateAllTotals();
                    }
                }
            });

            // Change event for select and quantity
            tableBody.addEventListener('change', function(e) {
                if (e.target.classList.contains('namabarang')) {
                    const row = e.target.closest('tr');
                    const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
                    row.querySelector('.harga').value = price;
                    calculateRowTotal(row);
                    calculateAllTotals();
                }
            });

            tableBody.addEventListener('input', function(e) {
                if (e.target.classList.contains('harga')) {
                    formatInputRibuan(e.target); // Format harga pake ribuan
                    const row = e.target.closest('tr');
                    calculateRowTotal(row);
                    calculateAllTotals();
                } else if (e.target.classList.contains('jumlah')) {
                    // Jangan format jumlah, cukup kalkulasi ulang
                    const row = e.target.closest('tr');
                    calculateRowTotal(row);
                    calculateAllTotals();
                }
            });


            // Add new row
            function addNewRow() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                  
                    <td>
                        <input type="text" name="namabarang[]" class="form-control namabarang" required>
                    </td>
                    <td>
                        <input type="text" name="harga[]" class="form-control harga" value="0" required>
                    </td>
                    <td>
                        <input type="number" name="jumlah[]" class="form-control jumlah" value="0" min="1" required>
                    </td>
                    <td>
                        <input type="text" class="form-control totaldisplay" value="Rp 0" readonly>
                        <input type="hidden" name="total[]" class="total" value="0">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">-</button>
                    </td>
                `;
                tableBody.appendChild(newRow);
            }

            // Calculate total per row
            function calculateRowTotal(row) {
                const qty = parseFloat(row.querySelector('.jumlah').value) || 0;
                const price = parseFormattedNumber(row.querySelector('.harga').value) || 0;
                const total = qty * price;

                row.querySelector('.total').value = total;
                row.querySelector('.totaldisplay').value = formatRupiah(total);
            }

            // Calculate grand total
            function calculateAllTotals() {
                let grand = 0;
                document.querySelectorAll('.total').forEach(el => {
                    grand += parseFloat(el.value) || 0;
                });

                document.getElementById('grandtotal').value = formatRupiah(grand);
                document.getElementById('grandtotalnon').value = grand;
            }

            // Format number to Rupiah
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(number);
            }

            // Initial calculation
            calculateAllTotals();
        });
    </script>
@endsection
