<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pembelian Admin</title>
    <style>
        body {
            -webkit-print-color-adjust: exact;
            padding: 30px;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        #table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px; 
        }

        #table th,
        #table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        #table th {
            background-color: #4CAF50;
            color: white;
            font-size: 12px;
        }

        .highlight {
            background-color: #06bbcc;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }

        @page {
            size: auto;
            margin: 0;
        }
    </style>
</head>

<body>
    <center>
        <h2 style="font-size: 16px;">Sumber Rejeki Official</h2>
        <h3 style="font-size: 14px;">Laporan Pembelian Admin</h3>
        <h4 style="font-size: 13px;">{{ date('d-m-Y', strtotime($tanggalAwal)) }} -
            {{ date('d-m-Y', strtotime($tanggalAkhir)) }}</h4>
    </center>
    <br>
    <table id="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Barang Masuk</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php $nomor = 1; @endphp
            @foreach ($pembelian as $item)
                <tr>
                    <td>{{ $nomor++ }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggalpembelian)) }}</td>
                    <td>{{ $item->namabarang }}</td>
                    <td>{{ number_format($item->harga, 2, ',', '.') }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td class="highlight">Total Pengeluaran :</td>
                <td class="highlight">{{ number_format($totalPengeluaran, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
