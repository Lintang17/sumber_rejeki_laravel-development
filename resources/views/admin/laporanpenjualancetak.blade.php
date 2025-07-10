<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan</title>
    <style>
        body {
            -webkit-print-color-adjust: exact;
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }

        h2,
        h4 {
            margin: 5px 0; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .biru {
            background-color: #06bbcc;
            color: white;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>Sumber Rejeki Official</h2>
    <h2>Laporan Penjualan: {{ $namaproduk }}</h2>
    <h4>{{ $namabulan . ' ' . $tahun }}</h4>

    <h3>Laporan Penjualan Kasir</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Nota</th>
                <th>Tanggal Penjualan</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $nomor = 1; @endphp
            @foreach ($penjualan as $item)
                <tr>
                    <td>{{ $nomor++ }}</td>
                    <td>{{ $item->kodenota }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}</td>
                    <td>{{ $item->namabarang }}</td>
                    <td>{{ number_format($item->harga, 2, ',', '.') }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>{{ number_format($item->total, 2, ',', '.') }}</td>
                    <td>{{ $item->status }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"></td>
                <td class="biru">Total:</td>
                <td class="biru">{{ number_format($totalPemasukan, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
