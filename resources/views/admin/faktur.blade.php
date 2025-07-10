<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - {{ $penjualan->kodenota }}</title>
    <style>
        body {
            font-family: Tahoma, sans-serif;
            font-size: 12px;
        }

        .container {
            width: 600px;
            margin: auto;
            border: 1px solid black;
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        @media print {
            .no-print {
                display: none;
            }
        }

        .text-right {
            text-align: right;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align: center;">FAKTUR PENJUALAN</h2>

        <table class="no-border" style="margin-top: 10px;">
            <tr>
                <td class="no-border" width="40%"><strong>No. Nota</strong></td>
                <td class="no-border">: {{ $penjualan->kodenota }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Tanggal</strong></td>
                <td class="no-border">: {{ date('d-m-Y', strtotime($penjualan->tanggalpenjualan)) }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Nama Pembeli</strong></td>
                <td class="no-border">: {{ $penjualan->namapembeli ?? '-' }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Telp</strong></td>
                <td class="no-border">: {{ $penjualan->notelp ?? '-' }}</td>
            </tr>
            <tr>
                <td class="no-border"><strong>Alamat</strong></td>
                <td class="no-border">: {{ $penjualan->alamat ?? '-' }}</td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Jumlah</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($penjualan->penjualandetail as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->namaproduk }}</td>
                        <td>{{ $item->jumlah_pembelian }}</td>
                        <td>{{ rupiah($item->harga) }}</td>
                        <td>{{ rupiah($item->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="margin-top: 10px;">
            <tr>
                <td class="text-right no-border" colspan="4"><strong>Grand Total</strong></td>
                <td><strong>{{ rupiah($penjualan->grandtotal) }}</strong></td>
            </tr>
            @if ($penjualan->dp > 0)
                <tr>
                    <td class="text-right no-border" colspan="4"><strong>DP</strong></td>
                    <td>{{ rupiah($penjualan->dp) }}</td>
                </tr>
                <tr>
                    <td class="text-right no-border" colspan="4"><strong>Status Pembayaran</strong></td>
                    <td>{{ $penjualan->statuspembayaran }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-right no-border" colspan="4"><strong>Sisa Pembayaran</strong></td>
                <td>{{ rupiah($penjualan->sisabayar) }}</td>
            </tr>
            <tr>
                <td class="text-right no-border" colspan="4"><strong>Uang Pembeli</strong></td>
                <td>{{ rupiah($penjualan->bayar) }}</td>
            </tr>
            <tr>
                <td class="text-right no-border" colspan="4"><strong>Kembalian</strong></td>
                <td>{{ rupiah($penjualan->kembali) }}</td>
            </tr>
        </table>

        <table style="margin-top: 40px; border: none;">
            <tr>
                <td class="no-border" style="text-align: center;">
                    <br><br>Penerima<br><br><br><br>(.....................)
                </td>
                <td class="no-border" style="text-align: center;">
                    <br><br>Hormat Kami<br><br><br><br>(.....................)
                </td>
            </tr>
        </table>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 8px 15px; font-size: 12pt;">Cetak</button>
            <a href="{{ url('admin/penjualandaftar') }}" style="padding: 8px 15px; font-size: 12pt;">Kembali</a>
        </div>
    </div>

    <script>
        window.print();
    </script>
</body>
</html>
