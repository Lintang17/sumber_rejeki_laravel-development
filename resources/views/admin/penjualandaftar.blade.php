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
                                    style="width: 220px; max-width: 90vw;">
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
                                <i class="mdi mdi-refresh"></i>
                                Reset
                            </a>
                        </form>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

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
                                @php $no = 1; @endphp
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
                                            $item->grandtotal ?? ($subtotalTransaksi - $totalDiskonTransaksi)
                                        );
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
                                            {{ rupiah($totalDiskonTransaksi) }}
                                        </td>
                                        <td class="text-center text-nowrap font-weight-bold">
                                            {{ rupiah($grandTotalTransaksi) }}
                                        </td>
                                        <td class="text-center">{{ rupiah($item->dp ?? 0) }}</td>
                                        <td class="text-center">{{ rupiah($item->bayar ?? 0) }}</td>
                                        <td class="text-center">{{ rupiah($item->sisabayar ?? 0) }}</td>
                                        <td class="text-center">{{ rupiah($item->kembali ?? 0) }}</td>
                                        <td class="text-center">{{ $item->metodepembayaran }}</td>
                                        <td class="text-center">{{ $item->statuspembayaran }}</td>
                                        <td class="text-center">
                                            @if($item->statuspengiriman == 'Selesai')
                                                <button class="btn btn-success btn-sm" disabled>
                                                    Selesai
                                                </button>
                                            @else
                                                <button class="btn btn-warning btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#statusModal{{ $item->idpenjualan }}">
                                                    {{ $item->statuspengiriman }}
                                                </button>
                                            @endif
                                        </td>
                                        <td class="text-center" style="min-width:140px;">
                                            <div style="display:flex;flex-direction:column;gap:6px;align-items:center;">
                                                @if($item->statuspembayaran == 'DP' && $item->sisabayar > 0)
                                                    <button
                                                        data-toggle="modal"
                                                        data-target="#edit{{ $item->idpenjualan }}"
                                                        class="btn btn-sm"
                                                        style="width:120px; background:#f59e0b; color:#fff; border:none; border-radius:8px; font-weight:600; box-shadow:0 2px 8px rgba(245,158,11,.25);">
                                                        <i class="mdi mdi-cash-fast mr-1"></i>
                                                        Pelunasan
                                                    </button>
                                                @else
                                                    <button
                                                        class="btn btn-sm"
                                                        disabled
                                                        style="width:120px; background:#22c55e; color:#fff; border:none; border-radius:8px; font-weight:600; opacity:1;">
                                                        <i class="mdi mdi-check-circle mr-1"></i>
                                                        Tuntas
                                                    </button>
                                                @endif
                                                <a href="{{ url('admin/nota',$item->notajual) }}"
                                                   target="_blank"
                                                   class="btn btn-sm"
                                                   style="width:120px; background:#0ea5e9; color:#fff; border:none; border-radius:8px; font-weight:600; box-shadow:0 2px 8px rgba(14,165,233,.25); display:flex; justify-content:center; align-items:center; gap:6px;">
                                                   <span style="transform:translateX(-5px);display:flex;align-items:center;">
                                                        <i class="mdi mdi-receipt mr-1"></i> Nota
                                                   </span>
                                                </a>
                                                </a>
                                                <a href="{{ url('admin/faktur',$item->notajual) }}"
                                                   target="_blank"
                                                   class="btn btn-sm"
                                                   style="width:120px; background:#2563eb; color:#fff; border:none; border-radius:8px; font-weight:600; box-shadow:0 2px 8px rgba(37,99,235,.25);">
                                                   <i class="mdi mdi-file-document mr-1"></i>
                                                   Faktur
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Modal Pelunasan --}}
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

                    {{-- Modal Ubah Status Pengiriman --}}
                    @if($item->statuspengiriman != 'Selesai')
                    <div class="modal fade"
                         id="statusModal{{ $item->idpenjualan }}"
                         tabindex="-1"
                         role="dialog">

                        <div class="modal-dialog" role="document">
                            <form action="{{ url('admin/penjualanupdate/' . $item->idpenjualan) }}"
                                    method="POST">
                                @csrf

                                <input type="hidden"
                                        name="statuspembayaran"
                                        value="{{ $item->statuspembayaran }}">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">
                                            Ubah Status Pengiriman
                                        </h5>

                                        <button type="button"
                                                class="close"
                                                data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <select name="statuspengiriman"
                                                class="form-control"
                                                required>

                                            <option value="Menunggu Konfirmasi"
                                                {{ $item->statuspengiriman == 'Menunggu Konfirmasi' ? 'selected' : '' }}>
                                                    Menunggu Konfirmasi
                                            </option>

                                            <option value="Dikirim"
                                                {{ $item->statuspengiriman == 'Dikirim' ? 'selected' : '' }}>
                                                Dikirim
                                            </option>

                                            <option value="Selesai">
                                                Selesai
                                            </option>
                                        </select>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit"
                                                class="btn btn-success">
                                            Simpan
                                        </button>

                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-dismiss="modal">
                                            Batal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif                 
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
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

</script>
@endsection
