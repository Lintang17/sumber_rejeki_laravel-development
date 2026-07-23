<!DOCTYPE html>
<html>
<head>
    <title>Surat Jalan - UD Sumber Rejeki</title>

    <style>
        *{
            box-sizing:border-box;
        }
        body{
            font-family:Arial,Helvetica,sans-serif;
            font-size:13px;
            color:#222;
            margin:0;
            padding:25px;
        }
        .container{
            width:100%;
        }
        .header{
            display:flex;
            justify-content:center;
            align-items:flex-start;
            border-bottom:3px solid #d8d8d8;
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
            font-size:27px;
            font-weight:bold;
            color:#0f72b8;
        }
        .company-sub{
            font-size:12px;
            color:#0f72b8;
            font-weight:bold;
            margin-top:3px;
        }
        .company-address{
            font-size:12px;
            color:#555;
            margin-top:3px;
        }
        .company-contact{
            font-size:12px;
            color:#0f72b8;
            margin-top:3px;
        }
        .title{
            text-align:center;
            font-size:30px;
            font-weight:bold;
            margin:25px 0;
            letter-spacing:2px;
        }
        table{
            width:100%;
        }
        .info td{
            padding:4px 0;
        }
        .label{
            width:150px;
            font-weight:bold;
        }
        .customer{
            margin:25px 0;
            border:1px solid #ccc;
            padding:15px;
            border-radius:6px;
            background:#fafafa;
        }
        .customer h4{
            margin:0 0 10px;
            color:#0f72b8;
        }
        .produk{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }
        .produk th{
            background:#efefef;
            border:1px solid #ccc;
            padding:10px;
        }
        .produk td{
            border:1px solid #ccc;
            padding:10px;
        }
        .text-center{
            text-align:center;
        }
        .footer{
            margin-top:70px;
        }
        .ttd{
            width:100%;
        }
        .ttd td{
            text-align:center;
            width:33%;
        }
        .garis{
            margin-top:70px;
            border-top:1px solid #000;
            width:180px;
            display:inline-block;
        }
        @media print{
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
            <img src="{{ public_path('assets/logo.png') }}">
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
                Telp. 0331-712787 | HP 081358826788
            </div>
        </div>
    </div>

    <div class="title">
        SURAT JALAN PENGIRIMAN
    </div>

    <table class="info">
        <tr>
            <td class="label">Kode PO</td>
            <td width="10">:</td>
            <td>{{ $po->kode_po }}</td>
        </tr>

        <tr>
            <td class="label">Tanggal PO</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($po->tanggal)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <td class="label">Tanggal Kirim</td>
            <td>:</td>
            <td>
                {{ $po->tanggal_dikirim ? \Carbon\Carbon::parse($po->tanggal_dikirim)->format('d-m-Y') : '-' }}
            </td>
        </tr>

    </table>

    <div class="customer">
        <h4>Penerima</h4>
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:140px;"><b>Nama Customer</b></td>
                <td style="width:10px;">:</td>
                <td>{{ strtoupper($po->customer) }}</td>
            </tr>
            <tr>
                <td><b>Alamat</b></td>
                <td>:</td>
                <td>{{ $po->alamat }}</td>
            </tr>
            <tr>
                <td><b>No. HP</b></td>
                <td>:</td>
                <td>{{ $po->no_hp }}</td>
            </tr>
        </table>
    </div>

    <table class="produk">
        <thead>
        <tr>
            <th width="50">No</th>
            <th>Nama Barang</th>
            <th width="80">Qty</th>
            <th width="140">Harga</th>
            <th width="150">Total</th>
        </tr>
        </thead>

        <tbody>
        @foreach($po->detail as $i=>$item)
        <tr>

            <td class="text-center">
                {{ $loop->iteration }}
            </td>

            <td>
                <b>{{ $item->produk }}</b>

                @if($item->deskripsi)
                    <br>
                    <small>{{ $item->deskripsi }}</small>
                @endif
            </td>
            <td class="text-center">
                {{ $item->qty }}
            </td>
            <td class="text-right">
                Rp {{ number_format($item->harga_jual,0,',','.') }}
            </td>
            <td class="text-right">
                Rp {{ number_format($item->qty * $item->harga_jual,0,',','.') }}
            </td>
        </tr>

        @endforeach
        </tbody>
       <tfoot>
            @php
                $grandTotal = $po->detail->sum(function ($d) {
                    return $d->qty * $d->harga_jual;
                });
            @endphp

            <tr style="background:#f3f3f3;font-weight:bold;">
                <td colspan="4"
                    style="
                        text-align:center;
                        padding:12px;
                        border:1px solid #ccc;
                        font-size:14px;">
                    TOTAL KESELURUHAN
                </td>
                <td style="
                        text-align:left;
                        padding:12px;
                        border:1px solid #ccc;
                        font-size:14px;">
                    Rp {{ number_format($grandTotal,0,',','.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <table class="ttd">
           <tr>
                <td style="width:50%;">
                    Admin
                </td>
                <td style="width:50%;">
                    Penerima (Customer)
                </td>
            </tr>
            <tr>
                <td>
                    <div class="garis"></div>
                </td>
                <td>
                    <div class="garis"></div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>

window.onload=function(){
    window.print();
}

window.onafterprint=function(){
    window.location.href="{{ url('admin/po') }}";
}

</script>

</body>
</html>