@extends('layouts.admin')

@section('content')

<div class="content-wrapper">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">

                    @php
                        $tahunSekarang = (int) date('Y');
                        $tahunAwal = 2025;
                        $tahunAkhir = $tahunSekarang + 5;

                        $tahunDipilih = (int) request('tahun', $tahunSekarang);
                        $bulanDipilih = (int) request('bulan', 0);

                        $daftarBulan = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                    @endphp

                    <div class="text-center"
                         style="margin-bottom:35px;">
                        <h3 class="font-weight-bold"
                            style="font-size:24px; margin-bottom:0;">
                            Laporan Penjualan Gudang
                        </h3>
                    </div>

                    <div style="display:flex;
                                flex-direction:row;
                                justify-content:space-between;
                                align-items:center;
                                flex-wrap:nowrap;
                                gap:10px;
                                width:100%;
                                overflow:visible;
                                position:relative;
                                z-index:100;
                                margin-bottom:30px;">

                        <form method="GET"
                              action="{{ url()->current() }}"
                              id="formFilterPeriode"
                              style="display:flex;
                                     flex-direction:row;
                                     align-items:center;
                                     flex-wrap:nowrap;
                                     gap:6px;
                                     margin:0;
                                     flex-shrink:0;">

                            <div class="dropdown" style="flex-shrink:0;">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm font-weight-bold text-dark dropdown-toggle"
                                        data-toggle="dropdown"
                                        style="white-space:nowrap; font-size:12px; padding:7px 9px;">
                                    <i class="mdi mdi-calendar-month"></i>
                                    Bulan{{ isset($daftarBulan[$bulanDipilih]) ? ': ' . substr($daftarBulan[$bulanDipilih], 0, 3) : '' }}
                                </button>

                                <div class="dropdown-menu p-2"
                                     style="width:220px; max-width:90vw;">
                                    <div class="row no-gutters">
                                        @foreach($daftarBulan as $angka => $nama)
                                            <div class="col-4 p-1">
                                                <label class="btn btn-sm btn-block mb-0 font-weight-bold
                                                    {{ $bulanDipilih === $angka ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                       style="cursor:pointer;">

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

                            <div class="dropdown" style="flex-shrink:0;">
                                <button type="button"
                                        class="btn btn-outline-secondary btn-sm font-weight-bold text-dark dropdown-toggle"
                                        data-toggle="dropdown"
                                        style="white-space:nowrap; font-size:12px; padding:7px 9px;">
                                    <i class="mdi mdi-calendar"></i>
                                    Tahun: {{ $tahunDipilih }}
                                </button>

                                <div class="dropdown-menu p-2"
                                     style="width:220px; max-width:90vw;">
                                    <div class="row no-gutters">
                                        @for($tahun = $tahunAwal; $tahun <= $tahunAkhir; $tahun++)
                                            <div class="col-4 p-1">
                                                <label class="btn btn-sm btn-block mb-0 font-weight-bold
                                                    {{ $tahunDipilih === $tahun ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                       style="cursor:pointer;">

                                                    <input type="radio"
                                                           name="tahun"
                                                           value="{{ $tahun }}"
                                                           class="d-none"
                                                           {{ $tahunDipilih === $tahun ? 'checked' : '' }}>

                                                    {{ $tahun }}
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <a href="{{ url()->current() }}"
                               class="btn btn-sm font-weight-bold"
                               style="background-color:#6f42c1;
                                      border-color:#6f42c1;
                                      color:#fff;
                                      white-space:nowrap;
                                      font-size:12px;
                                      padding:7px 9px;
                                      flex-shrink:0;">
                                <i class="mdi mdi-refresh"></i>
                                Reset
                            </a>

                        </form>

                        <div style="display:flex;
                                    flex-direction:row;
                                    align-items:center;
                                    flex-wrap:nowrap;
                                    justify-content:flex-end;
                                    gap:6px;
                                    flex-shrink:0;">

                            <a href="{{ route('laporan.gudang.penjualan.pdf', request()->query()) }}"
                               class="btn btn-danger btn-sm font-weight-bold"
                               style="white-space:nowrap;
                                      font-size:12px;
                                      padding:7px 9px;
                                      flex-shrink:0;">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                Download PDF
                            </a>

                            <a href="{{ route('laporan.gudang.penjualan.excel', request()->query()) }}"
                               class="btn btn-success btn-sm font-weight-bold"
                               style="white-space:nowrap;
                                      font-size:12px;
                                      padding:7px 9px;
                                      flex-shrink:0;">
                                <i class="bi bi-file-earmark-excel-fill"></i>
                                Download Excel
                            </a>

                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped"
                               id="table"
                               style="width:100%; min-width:850px;">

                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tanggal Penjualan</th>
                                    <th class="text-center">No Nota</th>
                                    <th class="text-center">Nama Produk</th>
                                    <th class="text-center">Jumlah Terjual</th>
                                    <th class="text-center">Harga Satuan</th>
                                    <th class="text-center">Total Penjualan</th>
                                    <th class="text-center">Status Pengiriman</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $nomor = 1; @endphp

                                @foreach ($data as $item)
                                    @foreach ($item->penjualandetail as $detail)
                                        <tr>
                                            <td class="text-center">{{ $nomor++ }}</td>

                                            <td class="text-center">
                                                {{ $item->tanggalpenjualan
                                                    ? \Carbon\Carbon::parse($item->tanggalpenjualan)->format('d-m-Y')
                                                    : '-' }}
                                            </td>

                                            <td class="text-center">
                                                {{ $item->notajual ?? '-' }}
                                            </td>

                                            <td>{{ $detail->namaproduk ?? '-' }}</td>

                                            <td class="text-center">
                                                {{ $detail->jumlah_pembelian ?? 0 }}
                                            </td>

                                            <td class="text-right text-nowrap">
                                                Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}
                                            </td>

                                            <td class="text-right text-nowrap">
                                                Rp {{ number_format($detail->total ?? 0, 0, ',', '.') }}
                                            </td>

                                            <td class="text-center">
                                                {{ $item->statuspengiriman ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                @if ($nomor === 1)
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Belum ada data penjualan.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll(
            '#formFilterPeriode input[type="radio"]'
        ).forEach(function (input) {

            input.addEventListener('change', function () {
                this.form.submit();
            });

        });
    });
</script>
@endsection