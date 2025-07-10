<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Opname</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Stok Opname</h2>
        <h3 style="margin: 0; font-weight: normal;">UD Sumber Rejeki</h3>
        <p style="margin: 0;">Jl. Gajah Mada No.197, Rambipuji, Jember</p>
        <p style="margin-top: 5px;">Periode: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Tanggal Opname</th>
                <th>Stok Sistem</th>
                <th>Stok Fisik</th>
                <th>Selisih</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->showroom->produksi->namaproduk ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_opname)->format('d-m-Y') }}</td>
                <td>{{ $item->stok_sistem }}</td>
                <td>{{ $item->stok_fisik }}</td>
                <td>{{ $item->selisih }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
