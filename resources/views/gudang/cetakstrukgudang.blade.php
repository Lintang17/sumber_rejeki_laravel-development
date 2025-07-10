<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Struk Penjualan Gudang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            color: #000;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18pt;
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

        @page {
            margin: 10mm;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2>STRUK PENJUALAN GUDANG</h2>
        <p><em>Tanggal: {{ date('d-m-Y', strtotime($pecah->tanggalpenjualan)) }}</em></p>
        <p><strong>No Nota: {{ $pecah->notajual }}</strong></p>
        <p><strong>Metode Pembayaran: {{ $pecah->metodepembayaran }}</strong></p>
    </div>

    {{-- Tabel detail barang --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Catatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no=1; @endphp
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

    {{-- Ringkasan pembayaran --}}
    <table>
        <tr>
            <td class="right" colspan="6"><strong>Subtotal</strong></td>
            <td class="right">{{ number_format($subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right" colspan="6"><strong>Diskon</strong></td>
            <td class="right">{{ $pecah->diskon }} %</td>
        </tr>
        <tr>
            <td class="right" colspan="6"><strong>DP</strong></td>
            <td class="right">{{ number_format($dp ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right" colspan="6"><strong>Total Belanja</strong></td>
            <td class="right">{{ number_format($grandtotal ?? ($subtotal - ($subtotal * $pecah->diskon / 100) - ($dp ?? 0)), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right" colspan="6"><strong>Uang Pembeli</strong></td>
            <td class="right">{{ number_format($pecah->uangpembeli, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="right" colspan="6"><strong>Kembalian</strong></td>
            <td class="right">{{ number_format($pecah->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Tanda Tangan Admin Gudang --}}
    <br><br><br>
    <table style="width: 100%; margin-top: 40px; border: none;">
        <tr>
            <td style="text-align: center; border: none;">
                Hormat Kami,<br>
                Admin Gudang
                <br><br><br><br>
                ({{ Auth::user()->name }})
            </td>
        </tr>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        TERIMA KASIH ATAS PEMBELIAN ANDA
    </div>

    {{-- Tombol kembali (hanya tampil di layar) 
    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.history.back()" style="padding: 8px 15px; font-size: 14px; cursor: pointer;">
            Kembali
        </button>
    </div>
        --}}
</body>

</html>
