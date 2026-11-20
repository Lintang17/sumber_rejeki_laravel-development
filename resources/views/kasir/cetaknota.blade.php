<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota</title>

    <style>
        @page {
            width: 58mm;
            margin: 3mm;
        }
        body {
            font-family: tahoma;
            font-size: 8pt;
        }
        .garis {
            border: 0;
            border-top: 1px solid #000;
            width: 230px;
            margin: 5px auto;
        }
        .garis-produk {
            border: 0;
            border-top: 1px dashed #000;
            width: 100%;
            margin: 3px 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <center>
        <div style="font-size:14pt; font-weight:bold;">
            UD SUMBER REJEKI
        </div>
        <div style="font-size:10pt;">
            Jl. Gajah Mada No.197, Rambipuji, Jember
        </div>
        <div style="font-size:10pt;">
            No. Telp: 082140355834
        </div>
        <div style="height:10px;"></div>

        <table style="width:230px; font-size:11pt; font-family:calibri;" border="0">
            <tr>
                <td><strong>Nota</strong></td>
                <td>: {{ $penjualan->kodenota }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal</strong></td>
                <td>: {{ $penjualan->created_at
                    ? \Carbon\Carbon::parse($penjualan->created_at)->timezone('Asia/Jakarta')->format('d-m-Y, H.i') . ' WIB'
                    : '-' }}
                </td>
            </tr>
            <tr>
                <td><strong>Pembeli</strong></td>
                <td>: {{ $penjualan->namapembeli ?? '-' }}</td>
            </tr>
        </table>

        <hr class="garis">
        @php
            $subtotal = 0;
            $totalDiskon = 0;

            // Nilai 0 ditampilkan sebagai tanda -
            $formatRupiah = function ($nilai) {
                return (float) ($nilai ?? 0) == 0
                    ? '-'
                    : number_format($nilai, 0, ',', '.');
            };
        @endphp

        <table style="width:230px; font-size:11pt; font-family:calibri;" border="0">
            @foreach ($penjualan->penjualandetail as $detail)
                @php
                    $harga = (float) ($detail->harga ?? 0);
                    $jumlah = (int) ($detail->jumlah_pembelian ?? 0);
                    $persenDiskon = (float) ($detail->discount ?? 0);

                    $subtotalItem = $harga * $jumlah;
                    $totalItem = (float) ($detail->total ?? 0);

                    $subtotal += $subtotalItem;
                    $totalDiskon += max(0, $subtotalItem - $totalItem);
                @endphp

                <tr>
                    <td colspan="2">
                        {{ Str::limit($detail->namaproduk, 40) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align:left;">
                        {{ $formatRupiah($harga) }} x {{ $jumlah }}
                    </td>
                    <td style="text-align:right;">
                        {{ $formatRupiah($totalItem) }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size:9pt;">
                        Diskon:
                        {{ $persenDiskon > 0
                            ? number_format($persenDiskon, 0, ',', '.') . '%'
                            : '-' }}
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <hr class="garis-produk">
                    </td>
                </tr>
            @endforeach

        </table>
        <br>

        <table style="width:230px; font-size:11pt; font-family:calibri;" border="0">
            <tr>
                <td><strong>Sub Total</strong></td>
                <td style="text-align:right;">
                    {{ $formatRupiah($subtotal) }}
                </td>
            </tr>
            <tr>
                <td><strong>Total Diskon</strong></td>
                <td style="text-align:right;">
                    {{ $formatRupiah($totalDiskon) }}
                </td>
            </tr>
            <tr>
                <td><strong>Ongkir</strong></td>
                <td style="text-align:right;">
                    {{ $formatRupiah($penjualan->ongkir ?? 0) }}
                </td>
            </tr>
            <tr>
                <td><strong>Grand Total</strong></td>
                <td style="text-align:right;">
                    {{ $formatRupiah($penjualan->grandtotal) }}
                </td>
            </tr>

            @if (($penjualan->dp ?? 0) > 0)
                <tr>
                    <td><strong>DP</strong></td>
                    <td style="text-align:right;">
                        {{ $formatRupiah($penjualan->dp) }}
                    </td>
                </tr>
                <tr>
                    <td><strong>Sisa Bayar</strong></td>
                    <td style="text-align:right;">
                        {{ $formatRupiah($penjualan->sisabayar ?? 0) }}
                    </td>
                </tr>
            @endif

            <tr>
                <td><strong>Uang Pembeli</strong></td>
                <td style="text-align:right;">
                    {{ $formatRupiah($penjualan->bayar ?? 0) }}
                </td>
            </tr>
            <tr>
                <td><strong>Kembalian</strong></td>
                <td style="text-align:right;">
                    {{ $formatRupiah($penjualan->kembali ?? 0) }}
                </td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td style="text-align:right;">
                    {{ $penjualan->statuspembayaran }}
                </td>
            </tr>
        </table>

        <hr class="garis">

        <table style="width:230px; font-size:11pt;" cellspacing="2">
            <tr>
                <td align="center" style="font-size:9pt;">
                    <em>
                        Notes: Struk pembelian tidak boleh hilang, dibuang, dan rusak!
                    </em>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td align="center">
                    TERIMA KASIH ATAS PEMBELIAN ANDA
                </td>
            </tr>
        </table>

        <a href="{{ url('kasir/penjualandaftar') }}"
            class="no-print"
            style="margin-top:10px; padding:5px 10px; font-size:12pt; cursor:pointer;">
            Kembali
        </a>

    </center>

    <script>
        window.print();
    </script>

</body>
</html>