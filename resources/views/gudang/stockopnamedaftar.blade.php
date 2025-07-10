{{-- @extends('layouts.admin')

@section('content')
    <style>
        #modalTambah .modal-dialog {
            max-width: 70%;
        }
    </style>

    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Stock Opname</h4>

                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (Auth::user()->level != 'Owner')
                            <button type="button" class="btn btn-primary mb-3 mt-3" data-toggle="modal"
                                data-target="#modalTambah">
                                Tambah
                            </button>
                        @endif

                        <div class="table-responsive">
                            <table class="table" id="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Tanggal Stock Opname</th>
                                        <th width="50%" class="text-center">Daftar Produk</th>
                                        {{-- <th class="text-center">Aksi</th> --}}
                                    {{-- </tr>
                                </thead>
                                <tbody>
                                    @php $nomor = 1; @endphp
                                    @foreach ($stockOpnameDates as $date)
                                        @php $tanggalStockOpname = $date->tanggalstockopname; @endphp
                                        <tr>
                                            <td class="text-center">{{ $nomor }}</td>
                                            <td class="text-center">{{ date('d-m-Y', strtotime($tanggalStockOpname)) }}</td>
                                            <td>
                                                <table class="table table-borderless mb-0">
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th>Stok Gudang (Sistem)</th>
                                                        <th>Stok Sistem (Input)</th>
                                                        <th>Selisih</th>
                                                    </tr>
                                                    @foreach ($stockOpnameData[$tanggalStockOpname] as $item)
                                                        <tr>
                                                            <td width="30%">
                                                                {{ $item->produk->namaproduk ?? 'Produk Tidak Tersedia' }}
                                                            </td>
                                                            <td class="text-center" width="20%">{{ $item->stokgudang }}</td>
                                                            <td class="text-center" width="20%">{{ $item->stoksistem }}</td>
                                                            <td class="text-center" width="20%">{{ abs($item->stoksistem - $item->stokgudang) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </td>
                                            {{-- <td class="text-center">
                                                <a href="{{ url('gudang/stockopnamehapus', $tanggalStockOpname) }}"
                                                    class="btn btn-danger mb-1"
                                                    onclick="return confirm('Apakah anda yakin ingin menghapus data ini?')">Hapus</a>
                                            </td> --}}
                                        {{-- </tr>
                                        @php $nomor++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-labelledby="modalTambahLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahLabel">Tambah Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="forms-sample" method="post" action="{{ url('gudang/stockopnamesimpan') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="">Tanggal Stock Opname</label>
                            <input class="form-control" name="tanggalstockopname" type="date" required value="{{ date('Y-m-d') }}" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Scan Barcode</label>
                            <input type="text" id="barcodeInput" class="form-control" placeholder="Scan Barcode" oninput="scanBarcode()">
                        </div>

                        <table class="table table-bordered table-striped" id="dynamic_field">
                            <tr>
                                <td width="30%">
                                    <div class="form-group namabarangharga">
                                        <label>Nama Barang</label>
                                        <select name="idproduk[]" class="form-control namabarang" onchange="updateStokSistem(this)" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($produk as $item)
                                                <option value="{{ $item->idproduk }}" data-stok="{{ $item->stok }}">
                                                    {{ $item->namaproduk }} - {{ $item->barcode }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label>Stok Gudang (Sistem)</label>
                                        <input type="text" name="stokgudang[]" class="form-control stok-gudang" readonly>
                                    </div>
                                </td>
                                <td width="15%">
                                    <div class="form-group">
                                        <label>Stok Sistem (Input Manual)</label>
                                        <input type="number" name="stoksistem[]" class="form-control stok-sistem" min="0" oninput="calculateSelisih(this)" required>
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
                                        <button type="button" name="add" class="btn btn-success mt-4" onclick="addRow()">+</button>
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
                var rows = document.querySelectorAll("#dynamic_field tr");
                var targetRow = null;

                for (var i = 0; i < rows.length; i++) {
                    var stokGudangInput = rows[i].querySelector(".stok-gudang");
                    if (!stokGudangInput.value) {
                        targetRow = rows[i];
                        break;
                    }
                }

                if (!targetRow) {
                    addRow();
                    rows = document.querySelectorAll("#dynamic_field tr");
                    targetRow = rows[rows.length - 1];
                }

                if (targetRow) {
                    var selectElement = targetRow.querySelector("select[name='idproduk[]']");

                    fetch("{{ url('api/produkgetByBarcode') }}/" + barcode)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                var produkData = data.data;
                                if (produkData) {
                                    selectElement.value = produkData.idproduk;
                                    var stok = selectElement.options[selectElement.selectedIndex].getAttribute("data-stok");
                                    targetRow.querySelector(".stok-gudang").value = stok;
                                    calculateSelisih(targetRow.querySelector(".stok-sistem"));
                                    document.getElementById('barcodeInput').value = '';
                                } else {
                                    alert("Produk tidak ditemukan!");
                                }
                            }
                        });
                }
            }
        }

        function addRow() {
            var table = document.getElementById("dynamic_field");
            var newRow = table.rows[0].cloneNode(true);

            var inputs = newRow.getElementsByTagName("input");
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].value = '';
            }

            newRow.querySelector("select").value = '';
            newRow.querySelector(".stok-gudang").value = '';
            newRow.querySelector(".stok-sistem").value = '';
            newRow.querySelector(".selisih").value = '';

            var addButton = newRow.querySelector("button[name='add']");
            addButton.innerHTML = '-';
            addButton.setAttribute('onclick', 'removeRow(this)');
            addButton.classList.remove('btn-success');
            addButton.classList.add('btn-danger');

            table.appendChild(newRow);
        }

        function removeRow(button) {
            var row = button.closest("tr");
            row.remove();
        }

        function updateStokSistem(select) {
            var stok = select.options[select.selectedIndex].getAttribute("data-stok");
            var row = select.closest("tr");
            row.querySelector(".stok-gudang").value = stok;
            calculateSelisih(row.querySelector(".stok-sistem"));
        }

        function calculateSelisih(input) {
            var row = input.closest("tr");
            var stokSistem = parseInt(row.querySelector(".stok-sistem").value) || 0;
            var stokGudang = parseInt(row.querySelector(".stok-gudang").value) || 0;
            var selisih = Math.abs(stokSistem - stokGudang);
            row.querySelector(".selisih").value = selisih;
        }
    </script>
@endsection
