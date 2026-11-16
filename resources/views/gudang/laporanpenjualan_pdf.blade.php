<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Gudang</title>

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
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Penjualan Gudang</h2>
        <h3 style="margin: 0; font-weight: normal;">UD Sumber Rejeki</h3>
        <p style="margin: 0;">Jl. Gajah Mada No.197, Rambipuji, Jember</p>
        <p style="margin-top: 5px;">
            Periode: {{ \Carbon\Carbon::now()->format('d F Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Penjualan</th>
                <th>No Nota</th>
                <th>Nama Produk</th>
                <th>Jumlah Terjual</th>
                <th>Harga Satuan</th>
                <th>Total Penjualan</th>
                <th>Status Pengiriman</th>
            </tr>
        </thead>

        <tbody>
            @php $nomor = 1; @endphp

            @foreach ($data as $item)
                @foreach ($item->penjualandetail as $detail)
                    <tr>
                        <td>{{ $nomor++ }}</td>
                        <td>
                            {{ $item->tanggalpenjualan
                                ? \Carbon\Carbon::parse($item->tanggalpenjualan)->format('d-m-Y')
                                : '-' }}
                        </td>
                        <td>{{ $item->notajual ?? '-' }}</td>
                        <td>{{ $detail->namaproduk ?? '-' }}</td>
                        <td>{{ $detail->jumlah_pembelian ?? 0 }}</td>
                        <td>
                            Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}
                        </td>
                        <td>
                            Rp {{ number_format($detail->total ?? 0, 0, ',', '.') }}
                        </td>
                        <td>{{ $item->statuspengiriman ?? '-' }}</td>
                    </tr>
                @endforeach
            @endforeach

            @if ($nomor === 1)
                <tr>
                    <td colspan="8" style="text-align: center;">
                        Belum ada data penjualan.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
