<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faktur Penjualan - {{ $penjualan->kodenota }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
            background: #fff;
            margin: 0;
            padding: 15px;
        }
        .invoice {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            padding: 25px;
            background: #fff;
            border: 2px solid #222;
            outline: 1px solid #222;
            outline-offset: 4px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #222;
        }
        .invoice-title {
            font-size: 25px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .section-title {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 22px;
        }
        .info-table td {
            padding: 6px 3px;
        }
        .product-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .product-table th {
            background: #eeeeee;
            border: 1px solid #777;
            padding: 10px 5px;
        }
        .product-table td {
            border: 1px solid #aaa;
            padding: 9px 7px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .summary-table {
            width: 390px;
            margin-left: auto;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 13px;
        }
        .summary-table td {
            padding: 9px 12px;
            vertical-align: middle;
        }
        .summary-table td:first-child {
            width: 55%;
            text-align: left;
            white-space: nowrap;
        }
        .summary-table td:last-child {
            width: 45%;
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }
        .grand-total {
            width: 390px;
            margin: 0 0 0 auto;
            border-collapse: collapse;
            table-layout: fixed;
            border-top: 2px solid #222;
            border-bottom: 2px solid #222;
            font-size: 15px;
        }
        .grand-total td {
            padding: 14px 12px;
            font-weight: bold;
            white-space: nowrap;
        }
        .grand-total td:first-child {
            width: 55%;
            text-align: left;
        }
        .grand-total td:last-child {
            width: 45%;
            text-align: right;
            font-size: 17px;
            font-variant-numeric: tabular-nums;
        }
        .payment-section {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #bbb;
        }
        .payment-status td {
            border-top: 1px solid #999;
            padding-top: 12px;
            padding-bottom: 12px;
            font-weight: bold;
        }
        .signature-table {
            width: 100%;
            margin-top: 55px;
            border-collapse: collapse;
            font-size: 13px;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 65px;
        }
        .no-print {
            text-align: center;
            margin-top: 25px;
        }
        .btn {
            padding: 10px 22px;
            margin: 5px;
            border: none;
            color: white;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-back {
            background: #666;
        }
        .btn-print {
            background: #222;
        }

        @media print {
            body {
                padding: 0;
            }
            .invoice {
                width: 100% !important;
                max-width: none !important;
                margin: 0 auto !important;
                padding: 20px !important;
                border: 2px solid #222 !important;
                outline: none !important;
            }
            .no-print {
                display: none !important;
            }
            table,
            tr {
                page-break-inside: avoid;
            }
        }

        @media screen and (max-width: 600px) {
            .invoice {
                padding: 12px;
            }
            .summary-table,
            .grand-total {
                width: 100%;
            }
            .product-table {
                font-size: 10px;
            }
            .product-table th,
            .product-table td {
                padding: 6px 3px;
            }
        }
    </style>
</head>

<body>
    @php
        $no = 1;
        $totalDiskon = 0;
        $subtotal = 0;
    @endphp

    <div class="invoice">
        <div class="invoice-header">
            <div class="invoice-title">
                FAKTUR PENJUALAN
            </div>
        </div>

        <div class="section-title">
            INFORMASI TRANSAKSI
        </div>

        <table class="info-table">
            <tr>
                <td style="width: 125px;">No. Nota</td>
                <td style="width: 15px;">:</td>
                <td style="width: 40%;">
                    {{ $penjualan->kodenota ?? '-' }}
                </td>
                <td style="width: 100px;">Tanggal</td>
                <td style="width: 15px;">:</td>
                <td>
                    {{ date('d-m-Y', strtotime($penjualan->tanggalpenjualan)) }}
                </td>
            </tr>
            <tr>
                <td>Nama Pembeli</td>
                <td>:</td>
                <td>
                    {{ $penjualan->namapembeli ?? '-' }}
                </td>
                <td>No. Telepon</td>
                <td>:</td>
                <td>
                    {{ $penjualan->notelp ?? '-' }}
                </td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td colspan="4">
                    {{ $penjualan->alamat ?? '-' }}
                </td>
            </tr>
        </table>

        <div class="section-title">
            RINCIAN PEMBELIAN PRODUK
        </div>

        <table class="product-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th>Nama Produk</th>
                    <th style="width: 115px;">Harga</th>
                    <th style="width: 65px;">Jumlah</th>
                    <th style="width: 80px;">Diskon</th>
                    <th style="width: 125px;">Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($penjualan->penjualandetail as $item)
                    @php
                        $hargaSatuan = (float) ($item->harga ?? 0);
                        $jumlah = (int) ($item->jumlah_pembelian ?? 0);
                        $persenDiskon = (float) ($item->discount ?? 0);

                        $subtotalItem = $hargaSatuan * $jumlah;
                        $totalItem = (float) ($item->total ?? $subtotalItem);

                        $subtotal += $subtotalItem;
                        $totalDiskon += max(0, $subtotalItem - $totalItem);
                    @endphp

                    <tr>
                        <td class="text-center">
                            {{ $no++ }}
                        </td>
                        <td>
                            {{ $item->namaproduk }}
                        </td>
                        <td class="text-right">
                            {{ number_format($hargaSatuan, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            {{ $jumlah }}
                        </td>
                        <td class="text-center">
                            {{ $persenDiskon > 0
                                ? number_format($persenDiskon, 2, ',', '.') . '%'
                                : '-' }}
                        </td>
                        <td class="text-right">
                            {{ number_format($totalItem, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table" style="margin-top: 18px;">
            <tbody>
                <tr>
                    <td>Subtotal</td>
                    <td>
                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td>Total Diskon</td>
                    <td>
                        {{ $totalDiskon > 0
                            ? 'Rp ' . number_format($totalDiskon, 0, ',', '.')
                            : '-' }}
                    </td>
                </tr>
                <tr>
                    <td>Ongkos Kirim</td>
                    <td>
                        Rp {{ number_format($penjualan->ongkir ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="grand-total">
            <tr>
                <td>GRAND TOTAL</td>
                <td>
                    Rp {{ number_format($penjualan->grandtotal ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="payment-section">
            <div class="section-title">
                INFORMASI PEMBAYARAN
            </div>

            <table class="summary-table">
                <tbody>
                    @if ((float) ($penjualan->dp ?? 0) > 0)
                        <tr>
                            <td>DP</td>
                            <td>
                                Rp {{ number_format($penjualan->dp, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Sisa Pembayaran</td>
                            <td>
                                Rp {{ number_format($penjualan->sisabayar ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td>Uang Pembeli</td>
                        <td>
                            Rp {{ number_format($penjualan->bayar ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Kembalian</td>
                        <td>
                            {{ ($penjualan->kembali ?? 0) > 0
                                ? 'Rp ' . number_format($penjualan->kembali, 0, ',', '.')
                                : '-' }}
                        </td>
                    </tr>
                    <tr class="payment-status">
                        <td>Status Pembayaran</td>
                        <td>
                            {{ $penjualan->statuspembayaran ?? '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    Penerima
                    <div class="signature-space"></div>
                    (................................)
                </td>
                <td>
                    Hormat Kami,
                    <div class="signature-space"></div>
                    (................................)
                </td>
            </tr>
        </table>

        <div class="no-print">
            <button
                onclick="location.href='{{ url('admin/penjualandaftar') }}'"
                class="btn btn-back">
                Kembali
            </button>
            <button
                onclick="window.print()"
                class="btn btn-print">
                Cetak Faktur
            </button>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>
</html>