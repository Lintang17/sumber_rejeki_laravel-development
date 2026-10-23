<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota</title>

    <style>
        @page {
            width: 58mm;
            margin: 3mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0 auto;
            font-family: Tahoma, sans-serif;
            font-size: 8pt;
        }

        .nota-container {
            width: 230px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            line-height: 1.4;
        }

        .nama-toko {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .alamat {
            font-size: 9pt;
        }

        .telepon {
            font-size: 9pt;
            margin-top: 3px;
            margin-bottom: 8px;
        }

        .nota-table,
        .produk-table,
        .total-table,
        .footer-table {
            width: 230px;
            font-size: 9pt;
            font-family: Tahoma, sans-serif;
            border: none;
            border-collapse: collapse;
            text-align: left;
            margin: 0 auto;
        }

        .nota-table td {
            padding: 1px 0;
            vertical-align: top;
            text-align: left;
        }

        .nota-table td:first-child {
            width: 65px;
        }

        hr {
            border: none;
            border-top: 1px dashed black;
            margin: 5px auto;
            width: 230px;
        }

        .produk-table td {
            padding: 1px 0;
            vertical-align: top;
        }

        .nama-produk {
            text-align: left;
            overflow-wrap: anywhere;
            padding-top: 2px !important;
        }

        .harga-produk {
            text-align: left;
        }

        .nominal {
            text-align: right;
            white-space: nowrap;
        }

        .diskon {
            text-align: left;
            font-size: 8pt;
        }

        .total-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .total-table td:first-child {
            text-align: left;
            padding-right: 4px;
        }

        .total-table td:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .grand-total td {
            font-weight: bold;
            padding-top: 2px;
        }

        .footer-table {
            width: 230px;
            margin: 5px auto 0;
            border-collapse: collapse;
            font-family: Tahoma, sans-serif;
        }

        .footer-table td {
            font-size: 8pt;
            text-align: center;
            padding: 2px 0;
            line-height: 1.4;
        }

        .catatan {
            text-align: center !important;
        }

        .ucapan {
            text-align: center !important;
            padding-top: 12px !important;
        }

        .no-print {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 10px;
            font-size: 12pt;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="nota-container">
        <div class="header">
            <div class="nama-toko">UD SUMBER REJEKI</div>

            <div class="alamat">
                Jl. Gajah Mada No.197, Rambipuji, Jember
            </div>

            <div class="telepon">
                No. Telp: 082140355834
            </div>
        </div>

        <table class="nota-table">
            <tr>
                <td><strong>Nota</strong></td>
                <td>: {{ $penjualan->kodenota }}</td>
            </tr>

            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ date('d-m-Y H:i', strtotime($penjualan->tanggalpenjualan)) }}</td>
            </tr>

            <tr>
                <td><strong>Pembeli</strong></td>
                <td>: {{ $penjualan->namapembeli ?? '-' }}</td>
            </tr>
        </table>

        <hr>
        @php
            $subtotal = 0;
            $totalDiskon = 0;
        @endphp

        <!-- Daftar produk -->
        <table class="produk-table">
            @foreach ($penjualan->penjualandetail as $detail)
                @php
                    $harga = (float) ($detail->harga ?? 0);
                    $jumlah = (int) ($detail->jumlah_pembelian ?? 0);
                    $persenDiskon = (float) ($detail->discount ?? 0);

                    // Total sebelum diskon
                    $subtotalItem = $harga * $jumlah;

                    // Total setelah diskon
                    $totalItem = (float) ($detail->total ?? $subtotalItem);

                    $subtotal += $subtotalItem;
                    $totalDiskon += max(0, $subtotalItem - $totalItem);
                @endphp

                <tr>
                    <td colspan="2" class="nama-produk">
                        {{ $detail->namaproduk }}
                    </td>
                </tr>
                <tr>
                    <td class="harga-produk">
                        {{ number_format($harga, 0, ',', '.') }}
                        x {{ $jumlah }}
                    </td>

                    <td class="nominal">
                        {{ number_format($totalItem, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="diskon">
                        Diskon:
                        {{ $persenDiskon > 0
                            ? number_format($persenDiskon, 0, ',', '.') . '%'
                            : '-' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
            @endforeach
        </table>

        <table class="total-table">
            <tr>
                <td><strong>Sub Total</strong></td>
                <td>
                    {{ number_format($subtotal, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td><strong>Total Diskon</strong></td>
                <td>
                    {{ number_format($totalDiskon, 0, ',', '.') }}
                </td>
            </tr>

            <tr class="grand-total">
                <td>Grand Total</td>
                <td>
                    {{ number_format($penjualan->grandtotal, 0, ',', '.') }}
                </td>
            </tr>

            @if (($penjualan->dp ?? 0) > 0)
                <tr>
                    <td><strong>DP Awal</strong></td>
                    <td>
                        {{ number_format($penjualan->dp, 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td><strong>Sisa Pembayaran</strong></td>
                    <td>
                        {{ number_format(
                            max(0, $penjualan->grandtotal - $penjualan->dp),
                            0,
                            ',',
                            '.'
                        ) }}
                    </td>
                </tr>
            @endif

            <tr>
                <td><strong>Uang Pelunasan</strong></td>
                <td>
                    {{ number_format($penjualan->bayar ?? 0, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td><strong>Kembalian</strong></td>
                <td>
                    {{ number_format($penjualan->kembali ?? 0, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td><strong>Status</strong></td>
                <td>
                    {{ $penjualan->statuspembayaran }}
                </td>
            </tr>
        </table>

        <hr>
        <table class="footer-table">
            <tr>
                <td class="catatan">
                    <em>
                        Notes: struk pembelian tidak boleh hilang,
                        dibuang, dan rusak!
                    </em>
                </td>
            </tr>

            <tr>
                <td class="ucapan">
                    TERIMA KASIH ATAS PEMBELIAN ANDA
                </td>
            </tr>
        </table>

        <div style="text-align: center;">
            <a href="{{ url('admin/penjualandaftar') }}" class="no-print">
                Kembali
            </a>
        </div>
    </div>

    <script>
        window.print();
    </script>

</body>
</html>