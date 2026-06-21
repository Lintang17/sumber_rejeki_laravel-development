<!DOCTYPE html>
<html>
<head>
    <title>UD Sumber Rejeki</title>

    <style>
        *{ box-sizing:border-box; }

        body{
            font-family: Arial, Helvetica, sans-serif;
            color:#222;
            font-size:13px;
            margin:0;
            padding:25px;
        }

        .container{ width:100%; }

        .header{
            display:flex;
            align-items:flex-start;
            justify-content:center;
            border-bottom:3px solid #cfcfcf; 
            padding-bottom:12px;
            margin-bottom:20px;
            position:relative;
        }

        .logo{
            position:absolute;
            left:0;
            top:0;
        }

        .logo img{ width:80px; }

        .company{
            text-align:center;
        }

        .company-name{
            font-size:26px;
            font-weight:800;
            color:#1780c4;
        }

        .company-sub{
            font-size:12px;
            font-weight:bold;
            color:#1780c4;
        }

        .company-address,
        .company-contact{
            font-size:12px;
            color:#666;
        }

        .title{
            text-align:center;
            font-size:28px;
            font-weight:700;
            margin:15px 0 25px;
        }

        .info-box{
            margin-bottom:20px;
        }

        .info-box table{
            width:100%;
        }

        .info-box td{
            padding:3px 0;
        }

        .label{
            width:150px;
            font-weight:bold;
        }

        .product-table{
            width:100%;
            border-collapse:collapse;
        }

        .product-table th{
            background:#d9d9d9;
            padding:10px;
            text-align:center;
        }

        .product-table td{
            padding:10px;
            border-bottom:1px solid #ddd;
        }

        .text-center{ text-align:center; }

        .total-row td{
            font-weight:bold;
            background:#f5f5f5;
        }
    </style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="header">

        <div class="logo">
            <img src="{{ public_path('assets/logo.png') }}">
        </div>

        <div class="company">
            <div class="company-name">UD. SUMBER REJEKI</div>
            <div class="company-sub">CUSTOM DESIGN FURNITURE</div>
            <div class="company-address">JL. GAJAH MADA NO.197 RAMBIPUJI JEMBER</div>
        </div>

    </div>

    <!-- TITLE -->
    <div class="title">
        SURAT PENGAMBILAN BARANG
    </div>

    <!-- INFO ATAS -->
    <div class="info-box">
        <table>

            <tr>
                <td class="label">Customer</td>
                <td>: {{ strtoupper($po->customer) }}</td>
            </tr>

            <tr>
                <td class="label">Tanggal PO</td>
                <td>: {{ \Carbon\Carbon::parse($po->tanggal)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td class="label">Tanggal Ambil</td>
                <td>: {{ now()->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td class="label">Kode PO</td>
                <td>: {{ $po->kode_po }}</td>
            </tr>

            <tr>
                <td class="label">Alamat</td>
                <td>: {{ $po->alamat ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">No HP</td>
                <td>: {{ $po->no_hp ?? '-' }}</td>
            </tr>

        </table>
    </div>

    <!-- PRODUK -->
    <table class="product-table">

        <thead>
            <tr>
                <th>Nama Barang</th>
                <th width="80">Qty</th>
                <th width="140">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach($po->detail as $d)
            <tr>
                <td>{{ $d->produk }}</td>
                <td class="text-center">{{ $d->qty }}</td>
                <td class="text-center">
                    Rp {{ number_format($d->subtotal,0,',','.') }}
                </td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">TOTAL</td>
                <td class="text-center">
                    Rp {{ number_format($po->total,0,',','.') }}
                </td>
            </tr>
        </tfoot>

    </table>

    <br><br>

    <!-- TTD -->
    <table style="width:100%; margin-top:50px;">
        <tr>
            <td style="text-align:center;">
                Admin<br><br><br><br>
                (........................)
            </td>

            <td style="text-align:center;">
                Customer<br><br><br><br>
                (........................)
            </td>
        </tr>
    </table>

</div>

<script>
window.onload = function () {
    window.print();
}
</script>

</body>
</html>