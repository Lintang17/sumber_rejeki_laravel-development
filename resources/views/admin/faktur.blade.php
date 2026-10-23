<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $penjualan->kodenota }}</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 12px;
        }

        .container {
            width: 600px;
            margin: auto;
            border: 1px solid black;
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .text-right {
            text-align: right;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center;">FAKTUR PENJUALAN</h2>

        <table class="no-border" style="margin-top: 10px;">
            <tr>
                <td class="no-border" width="40%"><strong>No. Nota</strong></td>
                <td class="no-border">: {{ $penjualan->kodenota }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Tanggal</strong></td>
                <td class="no-border">: {{ date('d-m-Y', strtotime($penjualan->tanggalpenjualan)) }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Nama Pembeli</strong></td>
                <td class="no-border">: {{ $penjualan->namapembeli ?? '-' }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Telp</strong></td>
                <td class="no-border">: {{ $penjualan->notelp ?? '-' }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Alamat</strong></td>
                <td class="no-border">: {{ $penjualan->alamat ?? '-' }}</td>
            </tr>
        </table>

        <table cellspacing="0" cellpadding="3"
            style="width:100%; font-size:10pt; font-family:Calibri, sans-serif; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Diskon (%)</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                    $totalDiskon = 0;
                    $subtotal = 0;
                @endphp
                @foreach ($penjualan->penjualandetail as $item)
                    @php
                        $hargaSatuan = (float) ($item->harga ?? 0);
                        $jumlah = (int) ($item->jumlah_pembelian ?? 0);
                        $persenDiskon = (float) ($item->discount ?? 0);

                        // Subtotal sebelum diskon
                        $subtotalItem = $hargaSatuan * $jumlah;

                        // Total produk setelah diskon
                        $totalItem = (float) ($item->total ?? $subtotalItem);

                        $subtotal += $subtotalItem;
                        $totalDiskon += max(0, $subtotalItem - $totalItem);
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $no++ }}</td>
                        <td>{{ $item->namaproduk }}</td>
                        <td style="text-align: right;">
                            {{ number_format($hargaSatuan, 0, ',', '.') }}
                        </td>
                        <td style="text-align: center;">{{ $jumlah }}</td>
                        <td style="text-align: center;">
                            {{ $persenDiskon > 0
                                ? number_format($persenDiskon, 2, ',', '.') . '%'
                                : '-' }}
                        </td>
                        <td style="text-align: right;">
                            {{ number_format($totalItem, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Subtotal</strong>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                    {{ number_format($subtotal, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Total Diskon</strong>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                    {{ number_format($totalDiskon, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Grand Total</strong>
                </td>
                <td style="text-align: right; white-space: nowrap;">
                    <strong> {{ number_format($penjualan->grandtotal ?? 0, 0, ',', '.') }} </strong>
                </td>
            </tr>
            @if ((float) ($penjualan->dp ?? 0) > 0)
                <tr>
                    <td colspan="5" style="text-align: right;">
                        <strong>DP</strong>
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($penjualan->dp, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Sisa Pembayaran</strong>
                </td>
                <td style="text-align: right;">
                    {{ number_format($penjualan->sisabayar ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Status Pembayaran</strong>
                </td>
                <td style="text-align: right;">
                    {{ $penjualan->statuspembayaran ?? '-' }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Uang Pembeli</strong>
                </td>
                <td style="text-align: right;">
                    {{ number_format($penjualan->bayar ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;">
                    <strong>Kembalian</strong>
                </td>
                <td style="text-align: right;">
                    {{ number_format($penjualan->kembali ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <table style="margin-top: 40px; border: none;">
            <tr>
                <td class="no-border" style="text-align: center;">
                    <br><br>Penerima<br><br><br><br>(.....................)
                </td>
                <td class="no-border" style="text-align: center;">
                    <br><br>Hormat Kami<br><br><br><br>(.....................)
                </td>
            </tr>
        </table>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 8px 15px; font-size: 12pt;">Cetak</button>
            <a href="{{ url('admin/penjualandaftar') }}" style="padding: 8px 15px; font-size: 12pt;">Kembali</a>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
