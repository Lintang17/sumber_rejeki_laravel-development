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

        body {
            font-family: tahoma;
            font-size: 8pt;
        }

        hr {
            border-style: inset;
            border-width: 1px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <center>
        <!-- Header toko -->
        <div style="font-size:14pt; font-weight:bold;">UD SUMBER REJEKI</div>
        <div style="font-size:10pt;">Jl. Gajah Mada No.197, Rambipuji, Jember</div>
        <div style="font-size:10pt;">No. Telp: 082140355834</div>

        <!-- Informasi Nota -->
        <table style='width:230px; font-size:11pt; font-family:calibri;' border='0'>
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

        <!-- Daftar Produk -->
        <table style='width:230px; font-size:11pt; font-family:calibri;' border='0'>
            @foreach ($penjualan->penjualandetail as $detail)
                <tr>
                    <td colspan="2">{{ Str::limit($detail->namaproduk, 40) }}</td>
                </tr>
                <tr>
                    <td style='text-align:left;'>{{ number_format($detail->harga, 0, ',', '.') }} x {{ $detail->jumlah_pembelian }}</td>
                    <td style='text-align:right;'>{{ number_format($detail->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2">--------------------------------------------</td>
                </tr>
            @endforeach
        </table>

        <br>

        <!-- Ringkasan Pembayaran -->
        <table style='width:230px; font-size:11pt; font-family:calibri;' border='0'>
            <tr>
                <td><strong>Grand Total</strong></td>
                <td style="text-align:right;">{{ number_format($penjualan->grandtotal, 0, ',', '.') }}</td>
            </tr>
            @if ($penjualan->dp > 0)
                <tr>
                    <td><strong>DP Awal</strong></td>
                    <td style="text-align:right;">{{ number_format($penjualan->dp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Sisa Pembayaran</strong></td>
                    <td style="text-align:right;">{{ number_format($penjualan->grandtotal - $penjualan->dp, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td><strong>Uang Pelunasan</strong></td>
                <td style="text-align:right;">{{ number_format($penjualan->bayar ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Kembalian</strong></td>
                <td style="text-align:right;">{{ number_format($penjualan->kembali ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td style="text-align:right;">{{ $penjualan->statuspembayaran }}</td>
            </tr>
        </table>

        <hr>

        <table style='width:230px; font-size:11pt;' cellspacing='2'>
            <tr>
                <td align='center' style="font-size:9pt;">
                    <em>notes: struk pembelian tidak boleh hilang, dibuang, dan rusak!</em>
                </td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td> {{-- Spacer --}}
            </tr>
            <tr>
                <td align='center'>TERIMA KASIH ATAS PEMBELIAN ANDA</td>
            </tr>
        </table>

        <!-- Tombol kembali (hanya muncul di layar) -->
        <a href="{{ url('admin/penjualandaftar') }}" class="no-print"
            style="margin-top: 10px; padding: 5px 10px; font-size: 12pt; cursor: pointer;">Kembali</a>
    </center>

    <script>
        window.print();
    </script>
</body>
</html>
