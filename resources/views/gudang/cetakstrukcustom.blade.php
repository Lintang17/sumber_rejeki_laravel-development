<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk Custom Penjualan Gudang</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .info {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .ttd {
            width: 100%;
            margin-top: 60px;
            text-align: right;
        }

        .ttd .admin {
            margin-right: 50px;
        }

        @media print {
            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>STRUK CUSTOM PENJUALAN GUDANG</h2>
        <p><em>Tanggal: {{ date('d-m-Y', strtotime($pecah->tanggalpenjualan)) }}</em></p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>No Nota</strong></td>
                <td>{{ $pecah->notajual }}</td>
            </tr>
            <tr>
                <td><strong>Metode Pembayaran</strong></td>
                <td>{{ $pecah->metodepembayaran }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Catatan Custom</th>
                <th>Status</th> 
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($penjualan as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->namabarang }}</td>
                    <td class="right">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td class="right">{{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ $item->custom }}</td>
                    <td>{{ $item->statusgudang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <div class="ttd">
        <p class="admin">Admin Gudang</p>
        <br><br><br>
        <p class="admin">(___________________)</p>
    </div> --}}

</body>

</html>
