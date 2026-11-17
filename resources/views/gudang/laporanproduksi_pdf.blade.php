<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Produksi Barang</title>

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
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        td:nth-child(2) {
            text-align: left;
        }

        .kosong {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2 style="margin: 0;">
            Laporan Produksi Barang
        </h2>

        <h3 style="margin: 0; font-weight: normal;">
            UD Sumber Rejeki
        </h3>

        <p style="margin: 0;">
            Jl. Gajah Mada No.197, Rambipuji, Jember
        </p>

        <p style="margin-top: 5px;">
            Periode: {{ $periode }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Tanggal Produksi</th>
                <th>Stok Awal</th>
                <th>Stok Tambahan</th>
                <th>Stok Total</th>
                <th>HPP Final</th>
                <th>Harga Jual</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->namaproduk ?? '-' }}</td>
                    <td>
                        {{ $item->tanggalproduksi
                            ? \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y')
                            : '-' }}
                    </td>
                    <td>{{ $item->stok_awal ?? 0 }}</td>
                    <td>{{ $item->stok_tambahan ?? 0 }}</td>
                    <td>{{ $item->stok ?? 0 }}</td>
                    <td>
                        {{ $item->hppfinal !== null
                            ? 'Rp ' . number_format($item->hppfinal, 0, ',', '.')
                            : '-' }}
                    </td>
                    <td>
                        {{ $item->hargajual !== null
                            ? 'Rp ' . number_format($item->hargajual, 0, ',', '.')
                            : '-' }}
                    </td>
                    <td>{{ ucfirst($item->status ?? '-') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="kosong">
                        Belum ada data produksi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>