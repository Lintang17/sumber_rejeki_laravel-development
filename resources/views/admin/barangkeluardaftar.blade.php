@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Penjualan</h4>
                        <div class="table-responsive">
                            <table class="table" id="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">No. Nota</th>
                                        <th class="text-center">Tanggal Penjualan</th>
                                        <th width="30%" class="text-center">Daftar Produk</th>
                                        <th class="text-center">Total Harga Produk</th>
                                        <th class="text-center">Diskon</th>
                                        <th class="text-center">Total Belanja</th>
                                        <th class="text-center">Metode Pembayaran</th>
                                        @if (auth()->user()->role != 'Kasir')
                                            <th class="text-center">Status Pembayaran</th>
                                            <th class="text-center">Status Pengiriman</th>
                                        @endif
                                        @if (auth()->user()->role != 'Owner')
                                            <th class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $nomor = 1;
                                        $totalpemasukan = 0;
                                    @endphp
                                    @foreach ($penjualan as $item)
                                        <tr>
                                            <td>{{ $nomor++ }}</td>
                                            <td>{{ $item->notajual }}</td>
                                            <td>{{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}</td>
                                            <td width="40%">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga</th>
                                                    </tr>
                                                    @if (!empty($item->penjualandetail) && $item->penjualandetail->count() > 0)
                                                        @foreach ($item->penjualandetail as $detail)
                                                            <tr>
                                                                <td width="40%">{{ $detail->namabarang }}</td>
                                                                <td width="10%">{{ $detail->jumlah }}</td>
                                                                <td width="30%">{{ rupiah($detail->harga) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                                        </tr>
                                                    @endif

                                                </table>
                                            </td>
                                            @php
                                                $total_sebelum_diskon =
                                                    $item->diskon > 0
                                                        ? $item->grandtotal / (1 - $item->diskon / 100)
                                                        : $item->grandtotal;
                                                $totalpemasukan += $item->grandtotal;
                                            @endphp
                                            <td>{{ rupiah($total_sebelum_diskon) }}</td>
                                            <td>{{ $item->diskon }} %</td>
                                            <td>{{ rupiah($item->grandtotal) }}</td>
                                            <td>{{ $item->metodepembayaran }}</td>
                                            @if (auth()->user()->role != 'Kasir')
                                                @if ($item->statuspembelian == 'DP')
                                                    <td>
                                                        <button class="btn btn-warning text-white" data-toggle="modal"
                                                            data-target="#ubahStatusPembelian{{ $item->idpenjualan }}">
                                                            {{ $item->statuspembelian }}
                                                        </button>
                                                    </td>
                                                @else
                                                    <td>
                                                        <button class="btn btn-warning text-white">
                                                            {{ $item->statuspembelian }}
                                                        </button>
                                                    </td>
                                                @endif
                                                <td>
                                                    <button class="btn btn-warning text-white" data-toggle="modal"
                                                        data-target="#ubahStatus{{ $item->idpenjualan }}">
                                                        {{ $item->statustransaksi }}
                                                    </button>
                                                </td>
                                            @endif
                                            @if (auth()->user()->role != 'Owner')
                                                <td>
                                                    <button class="btn btn-success text-white mb-1" data-toggle="modal"
                                                        data-target="#detail{{ $item->notajual }}">Detail</button>
                                                    <a class="btn btn-info text-white mb-1"
                                                        href="{{ url('admin/cetakfaktur', $item->notajual) }}"
                                                        target="_blank">Faktur</a>
                                                    <a class="btn btn-info text-white mb-1"
                                                        href="{{ url('admin/cetaknota', $item->notajual) }}"
                                                        target="_blank">Nota</a>
                                                    {{-- <button class="btn btn-danger mb-1"
                                                        onclick="hapusData('{{ $item->notajual }}')">Hapus</button>
                                                        --}}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right"><em>Total Pemasukan :</em></th>
                                        <th colspan="3">{{ rupiah($totalpemasukan) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @foreach ($penjualan as $key => $item)
                            <!-- Modal Detail -->
                            <!-- Modal Detail -->
                            <div class="modal fade" id="detail{{ $item->notajual }}" tabindex="-1" role="dialog"
                                aria-labelledby="modalLabel{{ $item->notajual }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <form action="{{ url('admin/updatecustomproduk') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="notajual" value="{{ $item->notajual }}">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detail Penjualan - Nota: {{ $item->notajual }}</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Nama Barang</th>
                                                            <th>Harga</th>
                                                            <th>Jumlah</th>
                                                            <th>Total Harga</th>
                                                            <th>Custom</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $no = 1; @endphp
                                                        @if ($item->penjualandetail && $item->penjualandetail->count() > 0)
                                                            @foreach ($item->penjualandetail as $detail)
                                                                <tr>
                                                                    <td>{{ $no++ }}</td>
                                                                    <td>{{ $detail->namabarang }}</td>
                                                                    <td>{{ rupiah($detail->harga) }}</td>
                                                                    <td>{{ $detail->jumlah }}</td>
                                                                    <td>{{ rupiah($detail->total) }}</td>
                                                                    <td>
                                                                        <input type="hidden" name="idpenjualan[]"
                                                                            value="{{ $detail->idpenjualan }}">
                                                                        <textarea name="custom[]" class="form-control" rows="1">{{ $detail->custom }}</textarea>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="6" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Update</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>


                            <!-- Modal Ubah Status -->
                            <div class="modal fade" id="ubahStatus{{ $item->idpenjualan }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Status Pengiriman</h5>
                                            <button type="button" class="close"
                                                data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <form action="{{ url('admin/ubahstatustransaksi') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="notajual" value="{{ $item->notajual }}">
                                                <select class="form-control" name="statustransaksi">
                                                    <option value="Diproses"
                                                        {{ $item->statustransaksi == 'Dikirim' ? 'selected' : '' }}>
                                                        Diproses</option>
                                                    <option value="Dikirim"
                                                        {{ $item->statustransaksi == 'Dikirim' ? 'selected' : '' }}>
                                                        Dikirim</option>
                                                    <option value="Selesai"
                                                        {{ $item->statustransaksi == 'Selesai' ? 'selected' : '' }}>
                                                        Selesai</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="ubahStatusPembelian{{ $item->idpenjualan }}" tabindex="-1"
                                role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Status Pembayaran</h5>
                                            <button type="button" class="close"
                                                data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <form action="{{ url('admin/ubahstatuspembelian') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="notajual" value="{{ $item->notajual }}">

                                                <label for="sisayangharusdibayar{{ $item->idpenjualan }}">Sisa Yang Harus
                                                    Dibayar</label>
                                                <input type="text" name="sisayangharusdibayar"
                                                    id="sisayangharusdibayar{{ $item->idpenjualan }}"
                                                    class="form-control"
                                                    value="{{ number_format($item->grandtotal - $item->dp, 0, ',', '.') }}"
                                                    readonly>

                                                <!-- Simpan nilai angka asli sisa bayar ke input hidden dengan ID unik -->
                                                <input type="hidden" id="sisaBayarValue{{ $item->idpenjualan }}"
                                                    value="{{ $item->grandtotal - $item->dp }}">

                                                <label for="uangpembeli{{ $item->idpenjualan }}">Uang Pembeli</label>
                                                <input type="text" name="uangpembeli"
                                                    id="uangpembeli{{ $item->idpenjualan }}" class="form-control">

                                                <label for="kembalian{{ $item->idpenjualan }}">Kembalian</label>
                                                <input type="text" name="kembalian"
                                                    id="kembalian{{ $item->idpenjualan }}" class="form-control" readonly>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Batal</button>
                                            </div>
                                        </form>

                                        <script>
                                            // Fungsi format angka jadi ribuan dengan titik
                                            function formatRibuan{{ $item->idpenjualan }}(angka) {
                                                if (angka === 0 || angka === '') return '0';
                                                return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                            }

                                            // Fungsi untuk mengubah string dengan format ribuan menjadi angka
                                            function parseFormattedNumber{{ $item->idpenjualan }}(str) {
                                                if (!str || str === '') return 0;
                                                return parseInt(str.replace(/\./g, '')) || 0;
                                            }

                                            // Tunggu sampai DOM siap untuk modal ini
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const uangPembeli{{ $item->idpenjualan }} = document.getElementById(
                                                    'uangpembeli{{ $item->idpenjualan }}');
                                                const inputKembalian{{ $item->idpenjualan }} = document.getElementById(
                                                    'kembalian{{ $item->idpenjualan }}');
                                                const sisaBayar{{ $item->idpenjualan }} = parseFloat(document.getElementById(
                                                    'sisaBayarValue{{ $item->idpenjualan }}').value);

                                                if (uangPembeli{{ $item->idpenjualan }}) {
                                                    uangPembeli{{ $item->idpenjualan }}.addEventListener('input', function() {
                                                        // Ambil nilai input dan hapus karakter non-angka
                                                        let nilaiInput = this.value.replace(/[^0-9]/g, '');

                                                        // Jika kosong, set ke 0
                                                        if (nilaiInput === '') {
                                                            this.value = '';
                                                            inputKembalian{{ $item->idpenjualan }}.value = '0';
                                                            return;
                                                        }

                                                        // Ubah ke integer
                                                        const angkaUang = parseInt(nilaiInput);

                                                        // Format ulang tampilan dengan titik ribuan
                                                        this.value = formatRibuan{{ $item->idpenjualan }}(angkaUang);

                                                        // Hitung kembalian
                                                        const hasilKembalian = angkaUang - sisaBayar{{ $item->idpenjualan }};

                                                        // Tampilkan kembalian (jika minus, tampilkan 0)
                                                        if (hasilKembalian >= 0) {
                                                            inputKembalian{{ $item->idpenjualan }}.value =
                                                                formatRibuan{{ $item->idpenjualan }}(hasilKembalian);
                                                        } else {
                                                            inputKembalian{{ $item->idpenjualan }}.value = '0';
                                                        }
                                                    });

                                                    // Handle paste event
                                                    uangPembeli{{ $item->idpenjualan }}.addEventListener('paste', function(e) {
                                                        setTimeout(() => {
                                                            this.dispatchEvent(new Event('input'));
                                                        }, 10);
                                                    });
                                                }
                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function hapusData(notajual) {
            Swal.fire({
                title: 'Apakah kamu yakin ingin dihapus?',
                text: "Data ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/admin/barangkeluarhapus/' + notajual;
                }
            });
        }
    </script>
@endsection
