<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print PO Gudang</title>
    <style>
        body{
            font-family: sans-serif;
            font-size: 13px;
        }

        .header{
            text-align:center;
            margin-bottom:20px;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th, td{
            border:1px solid #000;
            padding:8px;
            text-align:left;
            vertical-align:middle;
        }

        .no-border td{
            border:none;
            padding:3px 0;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>PRODUKSI PURCHASE ORDER</h2>
</div>

<table class="no-border">
    <tr>
        <td width="150">Kode PO</td>
        <td>: {{ $po->kode_po }}</td>
    </tr>

    <tr>
        <td>Customer</td>
        <td>: {{ $po->customer }}</td>
    </tr>

    <tr>
        <td>Alamat</td>
        <td>: {{ $po->alamat ?? '-' }}</td>
    </tr>

    <tr>
        <td>Total Jenis Produk</td>
        <td>: {{ $po->detail->count() }} Produk</td>
    </tr>

    <tr>
        <td>Tanggal Order</td>
        <td>:
            {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}
        </td>
    </tr>

    <tr>
        <td>Estimasi Produksi</td>
        <td>:
            @if($po->estimasi_awal && $po->estimasi_akhir)
                {{ \Carbon\Carbon::parse($po->estimasi_awal)->format('d M Y') }}
                -
                {{ \Carbon\Carbon::parse($po->estimasi_akhir)->format('d M Y') }}
            @else
                -
            @endif
        </td>
    </tr>
</table>

<br>

<table>
<thead>
<tr>
    <th width="40">No</th>
    <th>Nama Produk</th>
    <th>Deskripsi</th>
    <th width="60">Qty</th>
</tr>
</thead>

<tbody>
@foreach($po->detail as $index => $detail)
<tr>
    <td>
        {{ $index + 1 }}
    </td>

    <td>
        {{ $detail->produk }}
    </td>

    <td>
        {{ $detail->deskripsi ?? '-' }}
    </td>

    <td style="text-align:center">
        {{ $detail->qty }}
    </td>
</tr>
@endforeach

</tbody>
</table>

<br>
<strong>Keterangan Produksi:</strong>
<p>
    {{ $po->keterangan ?? '-' }}
</p>
</body>
</html>