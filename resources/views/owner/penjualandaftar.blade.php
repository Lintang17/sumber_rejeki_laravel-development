@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">              
                    <div class="header-penjualan">
                        <h4 class="card-title font-weight-bold mb-0">
                            Daftar Penjualan
                        </h4>               
                        @php
                            $tahunSekarang = (int) date('Y');
                            $bulanSekarang = (int) date('n');
                            $tahunAwal = 2025;
                            $tahunAkhir = $tahunSekarang + 5;
                            $tahunDipilih = (int) request('tahun', date('Y'));
                            $bulanDipilih = (int) request('bulan', date('n'));
                            $daftarBulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ];
                        @endphp

                        <form method="GET"
                            action="{{ url()->current() }}"
                            id="formFilterPeriode"
                            class="filter-penjualan">

                            {{-- FILTER BULAN --}}
                            <div class="dropdown">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm font-weight-bold text-dark dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                    <i class="mdi mdi-calendar-month"></i>
                                    Bulan: {{ substr($daftarBulan[$bulanDipilih], 0, 3) }}
                                </button>
                                <div class="dropdown-menu p-2"
                                    style="width:220px; max-width:90vw;">
                                    <div class="row no-gutters">
                                        @foreach($daftarBulan as $angka => $nama)
                                            <div class="col-4 p-1">
                                                <label class="btn btn-sm btn-block mb-0 font-weight-bold
                                                    {{ $bulanDipilih === $angka ? 'btn-primary' : 'btn-outline-secondary' }}">
                                                    <input type="radio"
                                                        name="bulan"
                                                        value="{{ $angka }}"
                                                        class="d-none"
                                                        {{ $bulanDipilih === $angka ? 'checked' : '' }}>
                                                    {{ substr($nama, 0, 3) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- FILTER TAHUN --}}
                            <div class="dropdown">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm font-weight-bold text-dark dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                    <i class="mdi mdi-calendar"></i>
                                    Tahun: {{ $tahunDipilih }}
                                </button>
                                <div class="dropdown-menu p-2"
                                    style="width:220px; max-width:90vw;">
                                    <div class="row no-gutters">
                                        @for($tahun = $tahunAwal; $tahun <= $tahunAkhir; $tahun++)
                                            <div class="col-4 p-1">
                                                <label class="btn btn-sm btn-block mb-0 font-weight-bold
                                                    {{ (string) $tahunDipilih === (string) $tahun
                                                        ? 'btn-primary'
                                                        : 'btn-outline-secondary' }}">
                                                    <input type="radio"
                                                        name="tahun"
                                                       value="{{ $tahun }}"
                                                       class="d-none"
                                                       {{ (string) $tahunDipilih === (string) $tahun ? 'checked' : '' }}>
                                                    {{ $tahun }}
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            {{-- RESET --}}
                            <a href="{{ url()->current() }}"
                                class="btn btn-light btn-sm font-weight-bold">
                                <i class="mdi mdi-refresh"></i> Reset
                            </a>
                        </form>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table font-weight-dark" id="table">
                            <thead class="font-weight-bold">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No. Nota</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Nama Pembeli</th>
                                    <th class="text-center">No. Telp</th>
                                    <th class="text-center">Alamat</th>
                                    <th width="35%" class="text-center">Daftar Produk</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Total Diskon</th>
                                    <th class="text-center">Ongkir</th>
                                    <th class="text-center">Grand Total</th>
                                    <th class="text-center">DP</th>
                                    <th class="text-center">Bayar</th>
                                    <th class="text-center">Sisa Bayar</th>
                                    <th class="text-center">Kembalian</th>
                                    <th class="text-center">Metode</th>
                                    <th class="text-center">Status Pembayaran</th>
                                    <th class="text-center">Status Pengiriman</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $no = 1;         
                                    $totalpemasukan = 0;
                                @endphp

                                @foreach ($penjualan as $item)
                                    @php
                                        $subtotalTransaksi = 0;
                                        $totalDiskonTransaksi = 0;
                                    @endphp

                                    @foreach ($item->penjualandetail ?? [] as $detail)
                                        @php
                                            $harga = (float) ($detail->harga ?? 0);
                                            $jumlah = (int) ($detail->jumlah_pembelian ?? 0);
                                            $subtotalBaris = $harga * $jumlah;
                                            $totalBaris = (float) ($detail->total ?? 0);
                                            $diskonBaris = max(0, $subtotalBaris - $totalBaris);

                                            $subtotalTransaksi += $subtotalBaris;
                                            $totalDiskonTransaksi += $diskonBaris;
                                        @endphp
                                    @endforeach

                                    @php
                                        $grandTotalTransaksi = (float) (
                                            $item->grandtotal ??
                                            ($subtotalTransaksi - $totalDiskonTransaksi + ($item->ongkir ?? 0))
                                        );

                                        // Hitung pemasukan berdasarkan pembayaran yang sudah diterima
                                        $statusPembayaran = strtolower(trim((string) $item->statuspembayaran));
                                        $dp = (float) ($item->dp ?? 0);
                                        $bayar = (float) ($item->bayar ?? 0);
                                        $kembali = (float) ($item->kembali ?? 0);
                                        if ($statusPembayaran === 'dp') {
                                            $totalpemasukan += max(0, $dp) + max(0, $bayar - $kembali);
                                        } elseif ($statusPembayaran === 'lunas') {
                                            $totalpemasukan += max(0, $dp) + max(0, $bayar - $kembali);
                                        }
                                    @endphp

                                    <tr class="{{ $item->sisabayar > 0 ? 'table-warning' : '' }}">
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td class="text-center">{{ $item->notajual }}</td>
                                        <td class="text-center">{{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}</td>
                                        <td class="text-center">{{ $item->namapembeli ?? '-' }}</td>
                                        <td class="text-center">{{ $item->notelp ?? '-' }}</td>
                                        <td class="text-center">{{ $item->alamat ?? '-' }}</td>
                                        {{-- DAFTAR PRODUK --}}
                                        <td>
                                            <table class="table table-sm table-bordered mb-0"
                                                style="width: 100%; font-size: 12px; min-width: 500px;">
                                                <thead>
                                                    <tr>
                                                        <th>Nama Produk</th>
                                                        <th class="text-right">Harga</th>
                                                        <th class="text-center">Jumlah</th>
                                                        <th>Jenis Diskon</th>
                                                        <th class="text-center">Diskon (%)</th>
                                                        <th class="text-right">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($item->penjualandetail as $detail)
                                                    @php
                                                        $harga = (float) ($detail->harga ?? 0);
                                                        $jumlah = (int) ($detail->jumlah_pembelian ?? 0);

                                                        $subtotalBaris = $harga * $jumlah;
                                                        $totalBaris = (float) ($detail->total ?? 0);
                                                        $diskonBaris = max(0, $subtotalBaris - $totalBaris);

                                                        $persenDiskon = $subtotalBaris > 0
                                                            ? ($diskonBaris / $subtotalBaris) * 100
                                                            : 0;

                                                        $jenisDiskon = strtolower(
                                                            trim((string) ($detail->jenis_diskon ?? ''))
                                                        );
                                                        if ($jenisDiskon === 'non_partai') {
                                                            $jenisDiskon = 'Non-Partai';
                                                        } elseif ($jenisDiskon === 'partai') {
                                                            $jenisDiskon = 'Partai';
                                                        } elseif ($jenisDiskon === '') {
                                                            $jenisDiskon = '-';
                                                        } else {
                                                            $jenisDiskon = ucwords(
                                                                str_replace('_', ' ', $jenisDiskon)
                                                            );
                                                        }
                                                    @endphp

                                                    <tr>
                                                        <td>{{ $detail->namaproduk }}</td>
                                                        <td class="text-right text-nowrap"> {{ rupiah($harga) }}</td>
                                                        <td class="text-center">
                                                            {{ $jumlah }}
                                                        </td>
                                                        <td class="text-center text-nowrap">
                                                            {{ $jenisDiskon }}
                                                        </td>
                                                        <td class="text-center text-nowrap">
                                                            {{ rtrim(rtrim(number_format($persenDiskon, 2, ',', '.'), '0'), ',') }}%
                                                        </td>
                                                        <td class="text-right text-nowrap">
                                                            {{ rupiah($totalBaris) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center">
                                                            Tidak ada data
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ rupiah($subtotalTransaksi) }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ $totalDiskonTransaksi == 0 ? '-' : rupiah($totalDiskonTransaksi) }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ (float) ($item->ongkir ?? 0) == 0 ? '-' : rupiah($item->ongkir) }}
                                        </td>
                                        <td class="text-center text-nowrap font-weight-bold">
                                            {{ rupiah($grandTotalTransaksi) }}
                                        </td>
                                        <td class="text-center">{{ (float) ($item->dp ?? 0) == 0 ? '-' : rupiah($item->dp) }}</td>
                                        <td class="text-center">{{ (float) ($item->bayar ?? 0) == 0 ? '-' : rupiah($item->bayar) }}</td>
                                        <td class="text-center">{{ (float) ($item->sisabayar ?? 0) == 0 ? '-' : rupiah($item->sisabayar) }}</td>
                                        <td class="text-center">{{ (float) ($item->kembali ?? 0) == 0 ? '-' : rupiah($item->kembali) }}</td>
                                        <td class="text-center">{{ $item->metodepembayaran }}</td>
                                        <td class="text-center">{{ $item->statuspembayaran }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" style="min-width:90px;" data-toggle="modal" data-target="#statusModal{{ $item->idpenjualan }}">
                                                {{ $item->statuspengiriman }}
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; min-width: 110px;">
                                                {{-- EDIT ONGKIR --}}
                                                <button type="button"
                                                    class="btn btn-sm"  
                                                    style="width: 110px; height: 34px; background: #4285a8 !important; color: #ffffff !important; border: none !important; border-radius: 5px; font-size: 12px; font-weight: 600;"
                                                    data-toggle="modal"
                                                    data-target="#editOngkir{{ $item->idpenjualan }}">
                                                    Edit Ongkir
                                                </button>
                                                {{-- PELUNASAN / TUNTAS --}}
                                                @if($item->statuspembayaran == 'DP' && $item->sisabayar > 0)
                                                    <button type="button"
                                                        class="btn btn-sm"
                                                        style="width: 110px; height: 34px; background: #ffaf03 !important; color: #000000 !important; border: none !important; border-radius: 5px; font-size: 12px; font-weight: 600;"
                                                        data-toggle="modal"
                                                        data-target="#edit{{ $item->idpenjualan }}">
                                                        Pelunasan
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm"
                                                        style="width: 110px; height: 34px; background: #0470ec !important; color: #ffffff !important; border: none !important; border-radius: 5px; font-size: 12px; font-weight: 600;"
                                                        disabled>
                                                        Tuntas
                                                    </button>
                                                @endif
                                                {{-- HAPUS --}}
                                                <form action="{{ route('owner.penjualan.hapus', $item->idpenjualan) }}"
                                                    method="POST"
                                                    class="form-hapus-penjualan"
                                                    data-nota="{{ $item->notajual }}"
                                                    style="width: 110px; margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm"
                                                        style="width: 110px; height: 34px; background: #ff4254 !important; color: #ffffff !important; border: none !important; border-radius: 5px; font-size: 12px; font-weight: 600;">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="12" class="text-right">
                                        <em>Total Pemasukan:</em>
                                    </th>
                                    <th colspan="7" class="text-center">
                                        {{ rupiah($totalpemasukan) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Modal Pelunasan 
                    @foreach ($penjualan as $item)
                        @if($item->statuspembayaran == 'DP' && $item->sisabayar > 0)
                        <div class="modal fade" id="edit{{ $item->idpenjualan }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ url('admin/penjualanupdate/' . $item->idpenjualan) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Pelunasan - Nota: {{ $item->notajual }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="statuspengiriman" value="{{ $item->statuspengiriman }}">
                                            <input type="hidden" name="statuspembayaran" value="DP">

                                            <div class="form-group">
                                                <label>Sisa Pembayaran</label>
                                                <input type="text" class="form-control" value="{{ rupiah($item->sisabayar) }}" readonly>
                                                <input type="hidden" id="sisabayar{{ $item->idpenjualan }}" value="{{ $item->sisabayar }}">
                                            </div>
                                            <div class="form-group">
                                                <label>Uang Pembeli</label>
                                                <input type="text" inputmode="numeric" name="uangpembeli" class="form-control" id="bayar{{ $item->idpenjualan }}" oninput="formatUang(this); hitungKembalian({{ $item->idpenjualan }})">
                                            </div>
                                            <div class="form-group">
                                                <label>Kembalian</label>
                                                <input type="text" class="form-control" id="kembalian{{ $item->idpenjualan }}" readonly>
                                            </div>
                                            <small class="text-danger d-none" id="errorBayar{{ $item->idpenjualan }}">Uang pembeli kurang dari sisa bayar!</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif

                        {{-- Modal Ubah Status Pengiriman 
                        <div class="modal fade" id="statusModal{{ $item->idpenjualan }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form action="{{ url('admin/penjualanupdate/' . $item->idpenjualan) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="statuspembayaran" value="{{ $item->statuspembayaran }}">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Ubah Status Pengiriman</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <select name="statuspengiriman" class="form-control" required>
                                                <option value="Menunggu Konfirmasi" {{ $item->statuspengiriman == 'Menunggu Konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                                <option value="Dikirim" {{ $item->statuspengiriman == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                <option value="Selesai" {{ $item->statuspengiriman == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach --}}

                    {{-- MODAL EDIT ONGKIR --}}
                    @foreach ($penjualan as $item)
                        <div class="modal fade"
                            id="editOngkir{{ $item->idpenjualan }}"
                            tabindex="-1"
                            role="dialog">

                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('owner.penjualan.updateOngkir', $item->idpenjualan) }}"
                                        method="POST">
                                        @csrf

                                        <div class="modal-header">
                                            <h5 class="modal-title font-weight-bold">
                                                Edit Ongkir Penjualan
                                            </h5>

                                            <button type="button"
                                                class="close"
                                                data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Nomor Nota</label>
                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ $item->notajual }}"
                                                    readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>Nama Pembeli</label>
                                                <input type="text"
                                                    class="form-control"
                                                    value="{{ $item->namapembeli ?? '-' }}"
                                                    readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>Ongkir</label>
                                                <input type="text"
                                                    name="ongkir"
                                                    class="form-control"
                                                    value="{{ !empty($item->ongkir) && $item->ongkir > 0 ? number_format((float) $item->ongkir, 0, ',', '.') : '' }}"
                                                    inputmode="numeric"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                                    required>

                                                <small class="text-muted">
                                                    Masukkan nominal ongkir yang telah disepakati.
                                                </small>
                                            </div>

                                            <div class="alert alert-info mb-0">
                                                Grand Total dan Sisa Bayar akan dihitung ulang
                                                setelah ongkir disimpan.
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-content-save"></i>
                                                Simpan Ongkir
                                            </button>
                                            <button type="button"
                                                class="btn text-white"
                                                style="background-color: #6c757d; border: none; font-weight: 600; padding: 10px 24px; min-height: 47px;"
                                                data-dismiss="modal">
                                                Batal
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #table > thead > tr > th {
        background: #ffffff !important;
    }

    #table > tbody > tr > td,
    #table > tfoot > tr > th,
    #table table th,
    #table table td {
        background: #fff3cd !important;
    }
    .header-penjualan {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    .filter-penjualan {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0;
    }
    #table_wrapper > .row:first-child {
        display: flex;
        align-items: center;
    }
    #table_wrapper .dataTables_filter {
        text-align: right;
    }
    #table_wrapper .dataTables_filter input {
        width: 180px;
    }
    @media (max-width: 767px) {
        .header-penjualan {
            align-items: flex-start;
            flex-direction: column;
        }
        #table_wrapper > .row:first-child {
            display: block;
        }
        #table_wrapper .dataTables_filter {
            text-align: left;
        }
        #table_wrapper .dataTables_filter input {
            width: 150px;
            max-width: 60%;
        }
    }
</style>

@endsection

@section('script')
<script>
    function hitungKembalian(id) {
        let bayarStr = document.getElementById('bayar' + id).value.replace(/\./g, '').replace(/Rp|\s/g, '');
        let bayar = parseInt(bayarStr || 0);
        let sisa = parseInt(document.getElementById('sisabayar' + id).value);
        let kembalian = bayar - sisa;

        let errorText = document.getElementById('errorBayar' + id);
        let kembalianInput = document.getElementById('kembalian' + id);

        if (bayar < sisa) {
            errorText.classList.remove('d-none');
            kembalianInput.value = 'Rp 0';
        } else {
            errorText.classList.add('d-none');
            kembalianInput.value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(kembalian);
        }
    }

    function formatUang(input) {
        let value = input.value.replace(/\D/g, '');
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }

    function tandaiPilihanFilter() {
        document.querySelectorAll(
            '#formFilterPeriode input[type="radio"]'
        ).forEach(function (input) {
            input.addEventListener('change', function () {
                const form = this.form;
                const nama = this.name;

                document.querySelectorAll(
                    '#formFilterPeriode input[name="' + nama + '"]'
                ).forEach(function (radio) {
                    const label = radio.closest('label');
                    if (!label) return;

                    label.classList.toggle('btn-primary', radio.checked);
                    label.classList.toggle(
                        'btn-outline-secondary',
                        !radio.checked
                    );
                });

                if (form) {
                    form.submit();
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        tandaiPilihanFilter();
    });

    // Konfirmasi hapus transaksi penjualan Owner
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.form-hapus-penjualan').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const nomorNota = this.dataset.nota;
                Swal.fire({
                    title: 'Hapus Transaksi Penjualan?',
                    html: `Apakah Anda yakin ingin menghapus transaksi nota <strong>${nomorNota}</strong>?<br><br>
                        <span style="color:#d33;">
                            Transaksi dapat dipulihkan melalui History Hapus.
                        </span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endsection