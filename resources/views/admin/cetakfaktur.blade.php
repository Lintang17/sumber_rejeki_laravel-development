<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Faktur Penjualan {{ $pecah->kodenota }}</title>
    <style>
        #tabel {
            font-size: 15px;
            border-collapse: collapse;
            width: 100%;
        }

        #tabel td,
        #tabel th {
            padding-left: 5px;
            border: 1px solid black;
            text-align: center;
        }

        #tabel th {
            background-color: #f2f2f2;
        }

        @page {
            margin: 3mm;
        }

        /* Sembunyikan tombol saat print */
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body style='font-family:tahoma; font-size:8pt; padding-top:50px;'>
    <center>
        <div style="border: solid 1px; width:450px; padding:15px;">
            <h2>Faktur Penjualan {{ $pecah->kodenota }}</h2>

            <table
                style='width:350px; font-family:calibri; font-size:12pt; border-collapse: collapse; margin-bottom: 20px;'
                border='0'>
                <tr>
                    <td width="100px"><b>No. Nota</b></td>
                    <td>: {{ $pecah->kodenota }}</td>
                </tr>
                <tr>
                    <td><b>Tanggal</b></td>
                    <td>: {{ date('d-m-Y', strtotime($pecah->tanggalpenjualan)) }}</td>
                </tr>
            </table>

            <table id="tabel" style="width: 350px; font-family: calibri; font-size: 12pt; margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Produk</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $nobelanja = 1; @endphp
                    @foreach ($penjualan as $detail)
                        <tr>
                            <td>{{ $nobelanja }}</td>
                            <td>{{ $detail->produk->barcode ?? '-' }}</td>
                            <td>{{ $detail->namabarang }}</td>
                            <td>{{ number_format($detail->harga, 2, ',', '.') }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ number_format($detail->total, 2, ',', '.') }}</td>
                        </tr>
                        @php $nobelanja++; @endphp
                    @endforeach
                    <tr>
                        <td colspan="5" style="text-align:right"><b>Grand Total</b></td>
                        <td><b>{{ number_format($subtotal, 2, ',', '.') }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right"><b>Diskon</b></td>
                        <td><b>{{ $pecah->diskon }} %</b></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right"><b>Total Beli</b></td>
                        <td><b>{{ number_format($grandtotal, 2, ',', '.') }}</b></td>
                    </tr>
                    @if ($pecah->dp > 0 && $pecah->statuspembelian == 'DP')
                        <tr>
                            <td colspan="5" style="text-align:right"><b>DP</b></td>
                            <td style="padding:5px;margin:5px"><b>{{ number_format($pecah->dp, 2, ',', '.') }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align:right"><b>Status Pembelian</b></td>
                            <td style="padding:5px;margin:5px"><b>DP</b></td>
                        </tr>
                    @elseif (($pecah->dp > 0 && $pecah->statuspembelian == 'Lunas') || $pecah->statuspembelian == 'Lunas')
                        <tr>
                            <td colspan="5" style="text-align:right"><b>Status Pembelian</b></td>
                            <td style="padding:5px;margin:5px"><b>Lunas</b></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="5" style="text-align:right"><b>Uang Pembeli</b></td>
                        <td><b>{{ number_format($pecah->uangpembeli, 2, ',', '.') }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align:right"><b>Kembalian</b></td>
                        <td><b>{{ number_format($pecah->kembalian, 2, ',', '.') }}</b></td>
                    </tr>
                </tbody>
            </table>

            <table
                style='width:350px; font-family: calibri; font-size: 12pt; border-collapse: collapse; border: 0; margin-top: 30px;'>
                <tr>
                    <td width="35">&nbsp;<br><br><br><br></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;Penerima <br><br><br><br><br>(.....................)</td>
                    <td width="60">&nbsp;<br><br><br><br></td>
                    <td>Hormat Kami, <br><br><br><br><br>(.....................)</td>
                </tr>
            </table>

            <!-- Tombol Kembali (tidak tampil saat print) -->
            <button class="no-print" onclick="location.href='{{ url('admin/barangkeluardaftar') }}'"
                style="margin: 5px; padding: 8px 15px; font-size: 12pt; cursor: pointer;">Kembali</button>

        </div>
    </center>

    <script>
        window.print();
    </script>
</body>

</html>
