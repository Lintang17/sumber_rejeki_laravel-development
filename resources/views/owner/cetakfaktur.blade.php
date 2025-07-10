{{-- <!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota</title>
    <style>
        #tabel {
            font-size: 15px;
            border-collapse: collapse;
        }

        #tabel td {
            padding-left: 5px;
            border: 1px solid black;
        }

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

        /* Sembunyikan tombol cetak saat print */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body style='font-family:tahoma; font-size:8pt;padding-top:50px;'>
    <center>
        <div style="border: solid 1px;width:450px;padding:15px">
            <br>
            <h2>Nota Penjualan {{ $pecah->kodenota }}</h2>
            <br>
            <table style='width:350px; font-size:16pt; font-family:calibri; border-collapse: collapse;' border='0'>
                <tr>
                    <td width="100px">
                        <span style="font-size:11pt">No. Nota</span> 
                    </td>
                    <td>
                        <span style="font-size:11pt"> : {{ $pecah->kodenota }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-size:11pt">Tanggal</span>
                    </td>
                    <td>
                        <span style="font-size:11pt"> : {{ date('d-m-Y', strtotime($pecah->tanggalpenjualan)) }}</span>
                    </td>
                </tr>
            </table>
            <br><br>
            <table cellspacing='0' cellpadding='0'
                style='width:350px; font-size:12pt; font-family:calibri; border-collapse: collapse;' border='1'>
                <thead>
                    <tr>
                        <th style="padding:5px;margin:5px">No</th>
                        <th width="40%">Kode Produk</th>
                        <th width="40%">Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $nobelanja = 1; @endphp
                    @foreach ($penjualan as $detail)
                        <tr>
                            <td align="center" style="padding:5px;margin:5px">{{ $nobelanja }}</td>
                            <td align="center">{{ $detail->produk->barcode }}</td>
                            <td align="center">{{ $detail->namabarang }}</td>
                            <td style="padding:5px;margin:5px">{{ number_format($detail->harga, 2, ',', '.') }}</td>
                            <td style="padding:5px;margin:5px">{{ $detail->jumlah }}</td>
                            <td style="padding:5px;margin:5px">{{ number_format($detail->total, 2, ',', '.') }}</td>
                        </tr>
                        @php $nobelanja++; @endphp
                    @endforeach
                    <tr>
                        <td colspan="5" style="text-align:right">Grand Total : &nbsp;</td>
                        <td class="text-success" style="padding:5px;margin:5px">
                            {{ number_format($subtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right">Diskon : &nbsp;</td>
                        <td class="text-success" style="padding:5px;margin:5px">{{ $pecah->diskon }} %</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right">Total Beli : &nbsp;</td>
                        <td class="text-success" style="padding:5px;margin:5px">
                            {{ number_format($grandtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right">Uang Pembeli : &nbsp;</td>
                        <td class="text-success" style="padding:5px;margin:5px">
                            {{ number_format($pecah->uangpembeli, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right">Kembalian : &nbsp;</td>
                        <td class="text-success" style="padding:5px;margin:5px">
                            {{ number_format($pecah->kembalian, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            <br><br>
            <table cellspacing='0' cellpadding='0'
                style='width:350px; font-size:12pt; font-family:calibri; border-collapse: collapse;' border='0'>
                <tr>
                    <td width="35"><br><br><br><br></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;Penerima <br><br><br><br><br>(.....................)</td>
                    <td width="60"><br><br><br><br></td>
                    <td>Hormat Kami, <br><br><br><br><br>(.....................)</td>
                </tr>
            </table>
            <br>
            <!-- Tombol Kembali dan Cetak -->
            <button class="no-print" onclick="location.href='{{ url('admin/barangkeluardaftar') }}'"
                style="margin: 5px; padding: 8px 15px; font-size: 12pt; cursor: pointer;">Kembali</button>
            {{-- <button class="no-print" onclick="window.print()"
                style="margin: 5px; padding: 8px 15px; font-size: 12pt; cursor: pointer;">Cetak</button> --}}
    
    {{--        </div>
     </center>

    <script>
        window.print();
    </script>
</body>

</html> --}}
