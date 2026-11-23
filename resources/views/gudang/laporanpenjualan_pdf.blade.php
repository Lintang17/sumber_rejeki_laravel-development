<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Gudang</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
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
        }
        th {
            background-color: #eee;
            text-align: center;
        }
        td.center {
            text-align: center;
        }
        td.right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Penjualan Gudang</h2>
        <h3 style="margin: 0; font-weight: normal;">UD Sumber Rejeki</h3>
        <p style="margin: 0;">Jl. Gajah Mada No.197, Rambipuji, Jember</p>
        <p style="margin-top: 5px;">
            Periode: {{ $periode }}
        </p>
    </div>

    @php
        $totalPemasukan = collect($data)->sum(function ($item) {
            return (float) ($item['total'] ?? 0);
        });
    @endphp

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No Referensi</th>
                <th>Referensi</th>
                <th>Nama Produk</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center">
                        {{ !empty($item['tanggal'])
                            ? \Carbon\Carbon::parse($item['tanggal'])->format('d-m-Y')
                            : '-' }}
                    </td>
                    <td class="center">
                        {{ $item['nomor_referensi'] ?? '-' }}
                    </td>
                    <td class="center">
                        {{ $item['referensi'] ?? '-' }}
                    </td>
                    <td>{{ $item['nama_produk'] ?? '-' }}</td>
                    <td class="center">
                        {{ $item['jumlah'] ?? 0 }}
                    </td>
                    <td class="right">
                        Rp {{ number_format((float) ($item['harga'] ?? 0), 0, ',', '.') }}
                    </td>
                    <td class="right">
                        Rp {{ number_format((float) ($item['total'] ?? 0), 0, ',', '.') }}
                    </td>
                    <td class="center">
                        {{ $item['status'] ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">
                        Belum ada data penjualan.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #e8f0fe;">
                <td colspan="7" style="text-align: right;">
                    TOTAL KESELURUHAN
                </td>
                <td class="right">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>