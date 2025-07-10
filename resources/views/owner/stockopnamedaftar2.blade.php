{{-- @extends('layouts.admin')

@section('content')
    {{-- <style>
        #modalTambah .modal-dialog {
            max-width: 70%;
        } 
    </style> --}}

    {{-- <div class="container-fluid px-4 py-3">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-primary text-white d-flex justify-content-between align-items-center border-0">
                        <h3 class="h5 mb-0">
                            <i class="bi bi-clipboard-data me-2"></i>Daftar Stock Opname
                        </h3>
                        @if (Auth::user()->level != 'Owner')
                            <button type="button" class="btn btn-light" data-toggle="modal" data-target="#modalTambah">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Stock Opname
                            </button>
                        @endif
                    </div>

                    <div class="card-body">
                        {{-- @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif --}}

                        {{-- <div class="stock-opname-list">
                            @forelse ($stockOpnameDates as $date)
                                @php
                                    $tanggalStockOpname = $date->tanggalstockopname;
                                    $stockOpnameItems = $stockOpnameData[$tanggalStockOpname];
                                @endphp

                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary mr-2">
                                                <i class="mdi mdi-calendar text-white"></i>
                                            </span>
                                            <h5 class="mb-0 text-dark">
                                                {{ date('d F Y', strtotime($tanggalStockOpname)) }}
                                            </h5>
                                        </div>
                                        <div>
                                            <a href="{{ url('admin/stockopnamehapus', $tanggalStockOpname) }}"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">
                                                <i class="bi bi-trash me-1"></i>Hapus
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            @foreach ($stockOpnameItems as $item)
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold">{{ $item->produk->namaproduk }}</span>
                                                        <small class="text-muted">Detail Stok</small>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-primary bg-opacity-10 text-white me-2">
                                                            Gudang: {{ $item->stokgudang }}
                                                        </span>
                                                        <span class="badge bg-info bg-opacity-10 text-black me-2">
                                                            Sistem: {{ $item->stoksistem }}
                                                        </span>
                                                        @php
                                                            $selisihClass =
                                                                $item->selisih == 0 ? 'bg-success' : 'bg-danger';
                                                        @endphp
                                                        <span
                                                            class="badge {{ $selisihClass }} bg-opacity-10 text-{{ $selisihClass }}">
                                                            Selisih: {{ $item->selisih }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info text-center" role="alert">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Tidak ada data Stock Opname
                                </div>
                            @endforelse
                        </div>

                        {{-- Pagination --}}
                        {{-- <div class="d-flex justify-content-center mt-4">
                            {{ $stockOpnameDates->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" method="post" action="{{ url('admin/stockopnamesimpan') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="">Tanggal Stock Opname</label>
                            <input class="form-control" name="tanggalstockopname" type="date" required
                                value="{{ date('Y-m-d') }}" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Scan Barcode</label>
                            <input type="text" id="barcodeInput" class="form-control" placeholder="Scan Barcode"
                                oninput="scanBarcode()">
                        </div>

                        <table class="table table-bordered table-striped" id="dynamic_field">
                            <tr>
                                <td width="30%">
                                    <div class="form-group namabarangharga">
                                        <label>Nama Barang</label>
                                        <select name="idproduk[]" class="form-control namabarang"
                                            onchange="updateStokSistem(this)" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($produk as $item)
                                                <option value="{{ $item->idproduk }}" data-stok="{{ $item->stok }}">
                                                    {{ $item->namaproduk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label>Stock Sistem</label>
                                        <input type="text" name="stok[]" class="form-control stok-sistem" readonly>
                                    </div>
                                </td>
                                <td width="15%">
                                    <div class="form-group">
                                        <label>Stock Gudang</label>
                                        <input type="number" name="stokgudang[]" class="form-control stok-gudang"
                                            min="0" oninput="calculateSelisih(this)" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label>Selisih</label>
                                        <input type="text" name="selisih[]" class="form-control selisih" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <button type="button" name="add" class="btn btn-success mt-4"
                                            onclick="addRow()">+</button>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <button type="submit" class="btn btn-primary mr-2 float-end mt-4">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function scanBarcode() {
            var barcode = document.getElementById('barcodeInput').value;

            if (barcode) {
                // Temukan semua baris yang ada
                var rows = document.querySelectorAll("#dynamic_field tr");
                var targetRow = null;

                // Cek baris pertama yang masih kosong pada "stok sistem"
                for (var i = 0; i < rows.length; i++) {
                    var stokSistemInput = rows[i].querySelector(".stok-sistem");
                    if (!stokSistemInput.value) {
                        targetRow = rows[i];
                        break;
                    }
                }

                // Jika tidak ada baris kosong, tambahkan baris baru
                if (!targetRow) {
                    addRow();
                    rows = document.querySelectorAll("#dynamic_field tr"); // Ambil ulang baris setelah penambahan
                    targetRow = rows[rows.length - 1]; // Ambil baris terakhir yang baru saja ditambahkan
                }

                // Proses barcode dan update baris yang ditemukan
                if (targetRow) {
                    var selectElement = targetRow.querySelector("select[name='idproduk[]']");

                    // Using fetch API instead of XMLHttpRequest
                    fetch("{{ url('api/produkgetByBarcode') }}/" + barcode)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                var produkData = data.data;
                                selectElement.value = produkData.idproduk;
                                var stokSistem = selectElement.options[selectElement.selectedIndex].getAttribute(
                                    "data-stok");
                                targetRow.querySelector(".stok-sistem").value = stokSistem;

                                // Menghitung selisih antara stok sistem dan stok gudang
                                calculateSelisih(targetRow.querySelector(".stok-gudang"));

                                // Kosongkan input scan barcode setelah pemindaian
                                document.getElementById('barcodeInput').value = '';
                            }
                        });
                }
            }
        }

        // Menambahkan baris baru produk
        function addRow() {
            var table = document.getElementById("dynamic_field");
            var newRow = table.rows[0].cloneNode(true); // Salin baris pertama

            // Kosongkan nilai input pada baris baru
            var inputs = newRow.getElementsByTagName("input");
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].value = '';
            }

            // Reset select option pada baris baru
            newRow.querySelector("select").value = '';
            newRow.querySelector(".stok-sistem").value = '';
            newRow.querySelector(".selisih").value = '';

            // Ganti tombol tambah dengan tombol hapus
            var addButton = newRow.querySelector("button[name='add']");
            addButton.innerHTML = '-';
            addButton.setAttribute('onclick', 'removeRow(this)');
            addButton.classList.remove('btn-success');
            addButton.classList.add('btn-danger');

            // Tambahkan baris baru ke dalam tabel
            table.appendChild(newRow);
        }

        // Menghapus baris produk
        function removeRow(button) {
            var row = button.closest("tr");
            row.remove();
        }

        // Update stok sistem ketika memilih produk
        function updateStokSistem(select) {
            var stokSistem = select.options[select.selectedIndex].getAttribute("data-stok");
            var row = select.closest("tr");
            row.querySelector(".stok-sistem").value = stokSistem;
            calculateSelisih(row.querySelector(".stok-gudang"));
        }

        // Menghitung selisih antara stok sistem dan stok gudang
        function calculateSelisih(input) {
            var row = input.closest("tr");
            var stokSistem = parseInt(row.querySelector(".stok-sistem").value) || 0;
            var stokGudang = parseInt(input.value) || 0;
            var selisih = stokGudang - stokSistem;
            row.querySelector(".selisih").value = selisih;
        }
    </script>
@endsection
