<!DOCTYPE html>
<html>
<head>
    <title>Print PO</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            color:#333;
            font-size:14px;
        }

        .container{
            width:100%;
            padding:20px;
        }

        .header{
            text-align:center;
            margin-bottom:30px;
        }

        .company-name{
            font-size:28px;
            font-weight:bold;
            color:#0072bc;
        }

        .company-sub{
            font-size:14px;
            margin-top:4px;
        }

        .po-title{
            text-align:center;
            font-size:24px;
            font-weight:bold;
            margin:30px 0;
        }

        .info-table{
            width:100%;
            margin-bottom:30px;
        }

        .info-table td{
            vertical-align:top;
            padding:5px;
        }

        .product-table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        .product-table th{
            background:#e5e5e5;
            padding:12px;
            text-align:left;
        }

        .product-table td{
            padding:12px;
            border-bottom:1px solid #ddd;
        }

        .text-right{
            text-align:right;
        }

        @media print{
            .no-print{
                display:none;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <div class="company-name">
            UD. SUMBER REJEKI
        </div>

        <div class="company-sub">
            CUSTOM DESIGN FURNITURE
        </div>

        <div class="company-sub">
            JL. GAJAH MADA NO.197 RAMBIPUJI JEMBER
        </div>
    </div>

    <div class="po-title">
        Purchase Order
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Tanggal Pesanan :</strong>
                {{ \Carbon\Carbon::parse($po->tanggal)->format('d-m-Y') }}
                <br><br>

                <strong>PO Number :</strong>
                {{ $po->kode_po }}
            </td>

            <td>
                <strong>Pembeli</strong><br>
                {{ strtoupper($po->customer) }}
            </td>

            <td>
                <strong>Penjual</strong><br>
                UD. SUMBER REJEKI <br>
                Jl. Gajahmada 197 <br>
                Rambipuji - Jember
            </td>
        </tr>
    </table>

    <table class="product-table">
        <thead>
            <tr>
                <th>NAMA BARANG</th>
                <th>QTY</th>
                <th>HARGA</th>
                <th class="text-right">TOTAL</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->detail as $detail)
            <tr>
                <td>
                    <strong>{{ $detail->produk }}</strong>
                    <br>
                    <small>
                        {{ $detail->deskripsi }}
                    </small>
                </td>

                <td>
                    {{ $detail->qty }}
                </td>

                <td>
                    Rp {{ number_format($detail->harga_jual,0,',','.') }}
                </td>

                <td class="text-right">
                    Rp {{ number_format($detail->subtotal,0,',','.') }}
                </td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" class="text-right">
                    <strong>Total</strong>
                </td>

                <td class="text-right">
                    <strong>
                        Rp {{ number_format($po->total,0,',','.') }}
                    </strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <br><br>

    <div style="text-align:right;">
        Jember,
        {{ now()->format('d M Y') }}
        <br><br><br><br>

        <strong>UD. SUMBER REJEKI</strong>
    </div>

</div>

<script>
window.print();
</script>

</body>
</html>