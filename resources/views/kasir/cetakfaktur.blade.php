<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan</title>
    <style>
        #tabel { font-size: 15px; border-collapse: collapse; }
        #tabel td { padding-left: 5px; border: 1px solid black; }
        @page { margin: 3mm; }
        hr { border-style: inset; border-width: 1px; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body style="font-family:tahoma; font-size:8pt; padding-top:50px;">
    <center>
        <div style="border: solid 1px; width: 450px; padding: 15px">
            <h2>Nota Penjualan {{ $pecah->kodenota }}</h2>

            <table style="width:350px; font-size:11pt; font-family:calibri;">
                <tr>
                    <td>No. Nota</td>
                    <td>: {{ $pecah->kodenota }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: {{ date('d-m-Y', strtotime($pecah->tanggalpenjualan)) }}</td>
                </tr>
                <tr>
                    <td>Nama Pembeli</td>
                    <td>: {{ $pecah->namapembeli ?? '-' }}</td>
                </tr>
                <tr>
                    <td>No. Telepon</td>
                    <td>: {{ $pecah->notelp ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>: {{ $pecah->alamat ?? '-' }}</td>
                </tr>
            </table>

            <br>
            <table cellspacing="0" cellpadding="0" style="width:350px; font-size:12pt; font-family:calibri; border-collapse: collapse;" border="1">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $nobelanja = 1; @endphp
                    @foreach ($penjualan as $detail)
                        <tr>
                            <td align="center">{{ $nobelanja++ }}</td>
                            <td align="center">{{ $detail->showroom->barcode ?? '-' }}</td>
                            <td align="center">{{ $detail->namaproduk }}</td>
                            <td align="right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td align="center">{{ $detail->jumlah_pembelian }}</td>
                            <td align="right">{{ number_format($detail->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" align="right"><strong>Grand Total</strong></td>
                        <td align="right">{{ number_format($pecah->grandtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if ($pecah->dp > 0)
                        <tr>
                            <td colspan="5" align="right"><strong>DP</strong></td>
                            <td align="right">{{ number_format($pecah->dp, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="5" align="right"><strong>Sisa Pembayaran</strong></td>
                            <td align="right">{{ number_format($pecah->sisabayar ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="5" align="right"><strong>Uang Pembeli</strong></td>
                        <td align="right">{{ number_format($pecah->bayar ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right"><strong>Kembalian</strong></td>
                        <td align="right">{{ number_format($pecah->kembali ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right"><strong>Status Pembayaran</strong></td>
                        <td align="right">{{ $pecah->statuspembayaran }}</td>
                    </tr>
                </tbody>
            </table>

            <br><br>
            <table style="width:350px; font-size:12pt; font-family:calibri;">
                <tr>
                    <td width="50%" align="center">Penerima<br><br><br><br><br>(.....................)</td>
                    <td width="50%" align="center">Hormat Kami,<br><br><br><br><br>(.....................)</td>
                </tr>
            </table>

            <br>
            <button class="no-print" onclick="location.href='{{ url('kasir/penjualandaftar') }}'"
                style="margin: 5px; padding: 8px 15px; font-size: 12pt; cursor: pointer;">Kembali</button>
        </div>
    </center>

    <script>
        window.print();
    </script>
</body>
</html>
