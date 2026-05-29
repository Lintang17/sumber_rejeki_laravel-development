<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print PO</title>

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
        }

        .no-border td{
            border:none;
            padding:3px 0;
        }

        .ttd{
            margin-top:50px;
            width:100%;
        }

        .ttd td{
            border:none;
            text-align:center;
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
        <td>Tanggal</td>
        <td>:
            {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}
        </td>
    </tr>

    <tr>
        <td>Estimasi</td>
        <td>:
            {{ $po->estimasi_akhir
            ? \Carbon\Carbon::parse($po->estimasi_akhir)->format('d M Y')
            : '-' }}
        </td>
    </tr>
</table>

<br>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Deskripsi</th>
            <th>Qty</th>
            <th>HPP</th>
            <th>Harga Jual</th>
        </tr>
    </thead>

    <tbody>

    @foreach($po->detail as $index => $detail)
    <tr>
        <td>{{ $index+1 }}</td>
        <td>{{ $detail->produk }}</td>
        <td>{{ $detail->deskripsi ?? '-' }}</td>
        <td>{{ $detail->qty }}</td>
        <td>
            Rp {{ number_format($detail->hpp_estimasi,0,',','.') }}
        </td>
        <td>
            Rp {{ number_format($detail->harga_jual,0,',','.') }}
        </td>
    </tr>
    @endforeach

    </tbody>
</table>

<br>

<strong>Keterangan:</strong>
<p>{{ $po->keterangan ?? '-' }}</p>

<table class="ttd">
    <tr>
        <td>
            Mengetahui,<br>
            Owner
            <br><br><br>
            _____________
        </td>

        <td>
            Gudang
            <br><br><br>
            _____________
        </td>
    </tr>
</table>

</body>
</html>