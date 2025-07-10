{{-- <!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota</title>
    <style>
        @page {
            margin: 3mm;
        }

        hr {
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-style: inset;
            border-width: 1px;
        }

        /* Sembunyikan tombol saat dicetak */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body style='font-family:tahoma; font-size:8pt;'>
    <center>
        <table style='width:230px; font-size:16pt; font-family:calibri; border-collapse: collapse;' border='0'>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td><span style="font-size:11pt">Nota</span></td>
                <td><span style="font-size:11pt"> : {{ $pecah->kodenota }}</span></td>
            </tr>
            <tr>
                <td><span style="font-size:11pt">Tanggal</span></td>
                <td><span style="font-size:11pt"> : {{ date('d-m-Y', strtotime($pecah->tanggalpenjualan)) }}</span></td>
            </tr>
        </table>

        <br>

        <table cellspacing='0' cellpadding='0'
            style='width:230px; font-size:11pt; font-family:calibri; border-collapse: collapse;' border='0'>
            @foreach ($penjualan as $detail)
                <tr>
                    <td>{{ Str::limit($detail->namabarang, 50) }}</td>
                </tr>
                <tr>
                    <td style='vertical-align:top; text-align:left;'>{{ number_format($detail->harga, 2, ',', '.') }} x
                        {{ $detail->jumlah }}</td>
                    <td style='vertical-align:top; text-align:right;'>{{ number_format($detail->total, 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">---------------------------------------------------</td>
                </tr>
            @endforeach
        </table>

        <br>

        <table style='width:230px; font-size:11pt; font-family:calibri; border-collapse: collapse;' border='0'>
            <tr>
                <td width="100px">
                    <div style='text-align:left'>Sub Total</div>
                </td>
                <td>: {{ number_format($subtotal, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td width="100px">
                    <div style='text-align:left'>Diskon</div>
                </td>
                <td>: {{ $pecah->diskon }} %</td>
            </tr>
            <tr>
                <td width="100px">
                    <div style='text-align:left'>Total</div>
                </td>
                <td>: {{ number_format($pecah->grandtotal, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td width="100px">
                    <div style='text-align:left'>Uang Pembeli</div>
                </td>
                <td>: {{ number_format($pecah->uangpembeli, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td width="100px">
                    <div style='text-align:left'>Kembalian </div>
                </td>
                <td>: {{ number_format($pecah->kembalian, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">--------------------------------------------------</td>
            </tr>
        </table>

        <table style='width:230px; font-size:11pt;' cellspacing='2'>
            <tr>
                <td align='center'>TERIMA KASIH ATAS PEMBELIAN ANDA</br></td>
            </tr>
        </table>

        <!-- Tombol Kembali (Hanya muncul di layar, tidak di print) -->
        <a href="{{ url('admin/barangkeluartambah') }}" class="no-print" onclick="window.history.back()"
            style="margin-top: 10px; padding: 5px 10px; font-size: 12pt; cursor: pointer;">Kembali</a>
    </center>
</body>

</html>

<script>
    window.print();
</script>
--}}