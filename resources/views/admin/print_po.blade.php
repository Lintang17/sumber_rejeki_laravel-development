<!DOCTYPE html>
<html>
<head>
    <title>UD Sumber Rejeki</title>

    <style>
        *{
            box-sizing:border-box;
        }

        body{
            font-family: Arial, Helvetica, sans-serif;
            color:#222;
            font-size:13px;
            margin:0;
            padding:25px;
        }

        .container{
            width:100%;
        }

        .header{
            display:flex;
            align-items:flex-start;
            justify-content:center;
            border-bottom:3px solid #cfcfcf; 
            padding-bottom:12px;
            margin-bottom:25px;
            position:relative;
        }

        .logo{
            position:absolute;
            left:0;
            top:0;
        }

        .logo img{
            width:80px;
        }

        .company{
            text-align:center;
        }

        .company-name{
            font-size:26px;
            font-weight:800;
            color:#1780c4;
            margin-bottom:3px;
        }

        .company-sub{
            color:#1780c4;
            font-size:12px;
            font-weight:bold;
            margin-bottom:2px;
        }

        .company-address{
            color:#666;
            font-size:12px;
            margin-bottom:2px;
        }

        .company-contact{
            color:#1780c4;
            font-size:12px;
        }

        .po-title{
            text-align:center;
            font-size:32px;
            font-weight:700;
            margin:15px 0 30px;
        }

        .order-info{
            margin-bottom:25px;
        }

        .order-info table{
            width:100%;
        }

        .order-info td{
            padding:2px 0;
        }

        .label{
            width:140px;
            font-weight:bold;
        }

        .party-table{
            width:100%;
            margin-bottom:35px;
        }

        .party-table td{
            vertical-align:top;
        }

        .party-title{
            font-weight:700;
            text-decoration:underline;
            margin-bottom:8px;
        }

        .party-box{
            line-height:1.6;
        }

        .product-table{
            width:100%;
            border-collapse:collapse;
        }

        .product-table thead th{
            background:#d9d9d9;
            padding:12px;
            font-size:12px;
            font-weight:700;
            text-align:center;
        }

        .product-table td{
            padding:12px;
            border-bottom:1px solid #ddd;
            font-size:12px;
        }

        .text-center{
            text-align:center;
        }

        .text-right{
            text-align:right;
        }

        .product-name{
            font-weight:bold;
        }

        .desc{
            font-size:11px;
            color:#666;
            margin-top:4px;
        }

        .total-row td{
            font-weight:bold;
            background:#f5f5f5;
        }

        @page{
            margin:10mm;
        }

        @media print{

            html, body{
                margin:0;
                padding:0;
            }

            body{
                padding:15px;
                -webkit-print-color-adjust:exact;
                print-color-adjust:exact;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">

        <div class="logo">
            <img src="{{ public_path('assets/logo.png') }}" alt="logo">
        </div>

        <div class="company">
            <div class="company-name">
                UD. SUMBER REJEKI
            </div>

            <div class="company-sub">
                CUSTOM DESIGN FURNITURE
            </div>

            <div class="company-address">
                JL. GAJAH MADA NO.197 RAMBIPUJI JEMBER
            </div>

            <div class="company-contact">
                TLP. 0331-712787 HP. 081358826788
                Email : sumberrejeki81@yahoo.co.id
            </div>
        </div>

    </div>

    <div class="po-title">
        Purchase Order
    </div>

    <div class="order-info">
        <table>
            <tr>
                <td class="label">Tanggal Pesanan</td>
                <td width="10">:</td>
                <td>
                    {{ \Carbon\Carbon::parse($po->tanggal)->format('d-m-Y') }}
                </td>
            </tr>

            <tr>
                <td class="label">PO Number</td>
                <td>:</td>
                <td>
                    {{ $po->kode_po }}
                </td>
            </tr>
        </table>
    </div>

    <table class="party-table">

        <tr>
            <td width="50%">
                <div class="party-title">
                    Pembeli
                </div>

                <div class="party-box">
                    {{ strtoupper($po->customer) }} <br>
                    Jember
                </div>
            </td>

            <td style="text-align:right;">
                <div class="party-title">
                    Penjual
                </div>

                <div class="party-box">
                    UD. SUMBER REJEKI <br>
                    Jl. Gajahmada 197 <br>
                    Kec. Rambipuji Kab. Jember
                </div>
            </td>
        </tr>
    </table>

    <table class="product-table">

        <thead>
            <tr>
                <th style="text-align:left;">
                    NAMA BARANG
                </th>
                <th width="80">
                    QTY
                </th>
                <th width="70">
                    SAT
                </th>
                <th width="120">
                    Harga
                </th>
                <th width="130">
                    Total
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->detail as $detail)
            <tr>

                <td>
                    <div class="product-name">
                        {{ $detail->produk }}
                    </div>

                    @if($detail->deskripsi)
                    <div class="desc">
                        {{ $detail->deskripsi }}
                    </div>
                    @endif
                </td>

                <td class="text-center">
                    {{ $detail->qty }}
                </td>

                <td class="text-center">
                    PCS
                </td>

                <td class="text-center">
                    Rp {{ number_format($detail->harga_jual,0,',','.') }}
                </td>

                <td class="text-center">
                    Rp {{ number_format($detail->subtotal,0,',','.') }}
                </td>

            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">
                    TOTAL
                </td>

                <td class="text-right">
                    Rp {{ number_format($po->total,0,',','.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
window.onload = function () {
    window.print();
}

window.onafterprint = function () {
    window.location.href = "{{ $redirectUrl }}";
}
</script>

</body>
</html>