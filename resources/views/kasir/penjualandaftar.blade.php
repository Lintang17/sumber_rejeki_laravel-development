@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3"
                            style="gap: 12px;">
                            <h4 class="card-title font-weight-bold mb-0">
                                Daftar Penjualan
                            </h4>

                            @php
                                $tahunSekarang = (int) date('Y');
                                $tahunAwal = 2025;
                                $tahunAkhir = $tahunSekarang + 5;
                                $tahunDipilih = request('tahun', $tahunSekarang);

                                $daftarBulan = [
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ];

                                $bulanDipilih = (int) request('bulan', 0);
                            @endphp

                            <form method="GET"
                                action="{{ url()->current() }}"
                                id="formFilterPeriode"
                                class="d-flex align-items-center flex-wrap mb-0"
                                style="gap: 8px; max-width: 100%;">

                                {{-- FILTER BULAN --}}
                                <div class="dropdown">
                                    <button type="button"
                                        class="btn btn-outline-secondary btn-sm font-weight-bold text-dark dropdown-toggle"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="mdi mdi-calendar-month"></i>
                                        Bulan{{ isset($daftarBulan[$bulanDipilih]) ? ': ' . substr($daftarBulan[$bulanDipilih], 0, 3) : '' }}
                                    </button>

                                    <div class="dropdown-menu p-2"
                                        style="width: 220px; max-width: 90vw;">
                                        <div class="row no-gutters">
                                            @foreach ($daftarBulan as $angka => $nama)
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
                                        style="width: 220px; max-width: 90vw;">
                                        <div class="row no-gutters">
                                            @for ($tahun = $tahunAwal; $tahun <= $tahunAkhir; $tahun++)
                                                <div class="col-4 p-1">
                                                    <label class="btn btn-sm btn-block mb-0 font-weight-bold
                                                        {{ (string) $tahunDipilih === (string) $tahun ? 'btn-primary' : 'btn-outline-secondary' }}">
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
                                    <i class="mdi mdi-refresh"></i>
                                    Reset
                                </a>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table" id="table">
                                <thead>
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
                                        <th class="text-center">Grand Total</th>
                                        <th class="text-center">DP</th>
                                        <th class="text-center">Uang Pembeli</th>
                                        <th class="text-center">Sisa Bayar</th>
                                        <th class="text-center">Kembalian</th>
                                        <th class="text-center">Metode</th>

                                        @if (auth()->user()->role != 'Kasir')
                                            <th class="text-center">Status</th>
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
                                        @php
                                            $subtotalTransaksi = 0;
                                            $totalDiskonTransaksi = 0;

                                            foreach ($item->penjualandetail ?? [] as $detail) {
                                                $subtotalBaris =
                                                    (float) ($detail->harga ?? 0) *
                                                    (int) ($detail->jumlah_pembelian ?? 0);

                                                $totalBaris = (float) ($detail->total ?? 0);

                                                $subtotalTransaksi += $subtotalBaris;
                                                $totalDiskonTransaksi += max(
                                                    0,
                                                    $subtotalBaris - $totalBaris
                                                );
                                            }

                                            $grandTotalTransaksi = (float) ($item->grandtotal ?? 0);

                                            $totalpemasukan +=
                                                (float) ($item->dp ?? 0) +
                                                (float) ($item->sisabayar ?? 0);

                                            $belumLunas = ($item->sisabayar ?? 0) > 0;
                                        @endphp

                                        <tr class="{{ $belumLunas ? 'table-warning' : '' }}">
                                            <td class="text-center">{{ $nomor++ }}</td>
                                            <td class="text-center">{{ $item->notajual }}</td>
                                            <td class="text-center">
                                                {{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}
                                            </td>
                                            <td class="text-center">{{ $item->namapembeli ?? '-' }}</td>
                                            <td class="text-center">{{ $item->notelp ?? '-' }}</td>
                                            <td class="text-center">{{ $item->alamat ?? '-' }}</td>

                                            {{-- DAFTAR PRODUK --}}
                                            <td>
                                                <table class="table table-sm table-bordered mb-0"
                                                    style="width: 100%; font-size: 12px;">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Produk</th>
                                                            <th>Harga</th>
                                                            <th class="text-center">Jumlah</th>
                                                            <th>Jenis Diskon</th>
                                                            <th>Diskon (%)</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @if ($item->penjualandetail && $item->penjualandetail->count())
                                                            @foreach ($item->penjualandetail as $detail)
                                                                <tr>
                                                                    <td>{{ $detail->namaproduk }}</td>

                                                                    <td class="text-nowrap">
                                                                        {{ rupiah($detail->harga ?? 0) }}
                                                                    </td>
                                                 
                                                                    <td class="text-center">
                                                                        {{ $detail->jumlah_pembelian ?? 0 }}
                                                                    </td>

                                                                    @php
                                                                        $showroom = $detail->showroom;

                                                                        $subtotalBaris = (float) ($detail->harga ?? 0)
                                                                            * (int) ($detail->jumlah_pembelian ?? 0);

                                                                        $totalBaris = (float) ($detail->total ?? 0);
                                                                        $persenDiskon = $subtotalBaris > 0
                                                                            ? (int) round(
                                                                                (($subtotalBaris - $totalBaris) / $subtotalBaris) * 100
                                                                            )
                                                                            : 0;

                                                                        $diskonNonPartai = (int) ($showroom->discount ?? 0);
                                                                        $diskonPartai = (int) ($showroom->discount_partai ?? 0);

                                                                        // Tentukan jenis diskon berdasarkan diskon yang paling sesuai.
                                                                        $jenisDiskon = '-';

                                                                        if ($persenDiskon > 0) {
                                                                            if ($diskonPartai > 0 && $diskonNonPartai > 0) {
                                                                                if ($persenDiskon === $diskonPartai) {
                                                                                    $jenisDiskon = 'Partai';
                                                                                } elseif ($persenDiskon === $diskonNonPartai) {
                                                                                    $jenisDiskon = 'Non-Partai';
                                                                                } else {
                                                                                    $jenisDiskon = 'Non-Partai';
                                                                                }
                                                                            } elseif ($diskonPartai > 0) {
                                                                                $jenisDiskon = 'Partai';
                                                                            } elseif ($diskonNonPartai > 0) {
                                                                                $jenisDiskon = 'Non-Partai';
                                                                            }
                                                                        }
                                                                    @endphp

                                                                    <td class="font-weight-bold">
                                                                        {{ $jenisDiskon }}
                                                                    </td>

                                                                    <td class="text-center text-nowrap">
                                                                        {{ $persenDiskon }} %
                                                                    </td>           
                                                                    
                                                                    <td class="text-nowrap">
                                                                        {{ rupiah($detail->total ?? 0) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="6" class="text-center">
                                                                    Tidak ada data
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </td>

                                            {{-- SUBTOTAL SEBELUM DISKON --}}
                                            <td class="text-center text-nowrap">
                                                {{ rupiah($subtotalTransaksi) }}
                                            </td>

                                            {{-- TOTAL DISKON --}}
                                            <td class="text-center text-nowrap">
                                                {{ rupiah($totalDiskonTransaksi) }}
                                            </td>

                                            {{-- GRAND TOTAL SETELAH DISKON --}}
                                            <td class="text-center text-nowrap">
                                                {{ rupiah($grandTotalTransaksi) }}
                                            </td>

                                            <td class="text-center text-nowrap">
                                                {{ rupiah($item->dp ?? 0) }}
                                            </td>

                                            <td class="text-center text-nowrap">
                                                {{ rupiah($item->bayar ?? 0) }}
                                            </td>

                                            <td class="text-center text-nowrap">
                                                {{ rupiah($item->sisabayar ?? 0) }}
                                            </td>

                                            <td class="text-center text-nowrap">
                                                {{ rupiah($item->kembali ?? 0) }}
                                            </td>

                                            <td class="text-center">
                                                {{ $item->metodepembayaran ?? '-' }}
                                            </td>

                                            @if (auth()->user()->role != 'Kasir')
                                                <td class="text-center">
                                                    <button class="btn btn-warning text-white"
                                                        data-toggle="modal"
                                                        data-target="#ubahStatus{{ $item->idpenjualan }}">
                                                        {{ $item->statustransaksi }}
                                                    </button>
                                                </td>
                                            @endif

                                            @if (auth()->user()->role != 'Owner')
                                                <td class="text-center align-middle" style="width:240px;">
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                            data-toggle="modal"
                                                            data-target="#detail{{ $item->notajual }}"
                                                            class="btn text-white"
                                                            style="background:#8B5E5E; border:none; min-width:75px; height:42px; font-size:13px; font-weight:600; border-radius:8px 0 0 8px;">
                                                            Detail
                                                        </button>

                                                        <a href="{{ url('kasir/cetakfaktur', $item->notajual) }}"
                                                            target="_blank"
                                                            class="btn text-white"
                                                            style="background:#D89A2B; border:none; min-width:75px; height:42px; font-size:13px; font-weight:600;">
                                                            Faktur
                                                        </a>

                                                        <a href="{{ url('kasir/cetaknota', $item->notajual) }}"
                                                            target="_blank"
                                                            class="btn text-white"
                                                            style="background:#2F8F83; border:none; min-width:75px; height:42px; font-size:13px; font-weight:600; border-radius:0 8px 8px 0;">
                                                            Nota
                                                        </a>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="9" class="text-right">
                                            <em>Total Pemasukan:</em>
                                        </th>
                                        <th colspan="{{ 5 + (auth()->user()->role != 'Kasir' ? 1 : 0) + (auth()->user()->role != 'Owner' ? 1 : 0) }}">
                                            {{ rupiah($totalpemasukan) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- MODAL DETAIL --}}
                        @foreach ($penjualan as $item)
                            <div class="modal fade" id="detail{{ $item->notajual }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                Detail Penjualan - Nota: {{ $item->notajual }}
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Produk</th>
                                                        <th>Harga</th>
                                                        <th>Jumlah</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @php $no = 1; @endphp

                                                    @foreach ($item->penjualandetail as $detail)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $detail->namaproduk }}</td>
                                                            <td>{{ rupiah($detail->harga) }}</td>
                                                            <td>{{ $detail->jumlah_pembelian }}</td>
                                                            <td>{{ rupiah($detail->total) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL UBAH STATUS --}}
                            <div class="modal fade" id="ubahStatus{{ $item->idpenjualan }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ url('kasir/ubahstatustransaksi') }}" method="POST">
                                            @csrf

                                            <div class="modal-header">
                                                <h5 class="modal-title">Ubah Status Pengiriman</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <input type="hidden" name="notajual" value="{{ $item->notajual }}">

                                                <select class="form-control" name="statustransaksi" required>
                                                    <option value="Dikirim"
                                                        {{ $item->statustransaksi == 'Dikirim' ? 'selected' : '' }}>
                                                        Dikirim
                                                    </option>
                                                    <option value="Selesai"
                                                        {{ $item->statustransaksi == 'Selesai' ? 'selected' : '' }}>
                                                        Selesai
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
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

    <script>
        function tandaiPilihanFilter() {
            document.querySelectorAll(
                '#formFilterPeriode input[type="radio"]'
            ).forEach(function(input) {
                input.addEventListener('change', function() {
                    const form = this.form;
                    const nama = this.name;

                    document.querySelectorAll(
                        '#formFilterPeriode input[name="' + nama + '"]'
                    ).forEach(function(radio) {
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

        document.addEventListener('DOMContentLoaded', function() {
            tandaiPilihanFilter();
        });
    </script>
@endsection