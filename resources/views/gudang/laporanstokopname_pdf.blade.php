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
            text-align: center;
        }

        th {
            background-color: #eee;
        }

        td.nama-produk,
        td.keterangan {
            text-align: left;
        }

        .empty {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0;">Laporan Stok Opname</h2>
        <h3 style="margin: 0; font-weight: normal;">UD Sumber Rejeki</h3>
        <p style="margin: 0;">Jl. Gajah Mada No.197, Rambipuji, Jember</p>
        <p style="margin-top: 5px;">Periode: {{ $periode }}</p>
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
            @forelse($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td class="nama-produk">
                        {{ $item->showroom->produksi->namaproduk ?? '-' }}
                    </td>

                    <td>
                        {{ $item->tanggal_opname
                            ? \Carbon\Carbon::parse($item->tanggal_opname)->format('d-m-Y')
                            : '-' }}
                    </td>

                    <td>{{ $item->stok_sistem ?? 0 }}</td>
                    <td>{{ $item->stok_fisik ?? 0 }}</td>
                    <td>{{ $item->selisih ?? 0 }}</td>

                    <td class="keterangan">
                        {{ $item->keterangan ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">
                        Belum ada data stok opname.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>