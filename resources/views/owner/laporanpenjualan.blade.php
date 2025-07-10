@extends('layouts.admin')

@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Laporan Penjualan</h4>
                            <form method="post" action="{{ url('admin/laporanpenjualan') }}">
                                @csrf
                                <div class="row mt-3 mb-3"> 
                                    <div class="col-md-3">
                                        <label>Pilih Tahun</label>
                                        <select name="tahun" class="form-control">
                                            <option value="" {{ request('tahun') == '' ? 'selected' : '' }}>Pilih
                                                Tahun</option>
                                            @for ($i = 2021; $i <= 2025; $i++)
                                                <option value="{{ $i }}"
                                                    {{ request('tahun') == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Pilih Bulan</label>
                                        <select name="bulan" class="form-control">
                                            <option value="" {{ request('bulan') == '' ? 'selected' : '' }}>Pilih
                                                Bulan</option>
                                            @foreach (['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ request('bulan') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Pilih Nama Produk</label>
                                        <select name="namabarang" class="form-control">
                                            <option value="">Pilih Produk</option>
                                            @foreach ($produk as $item)
                                                <option value="{{ $item->namaproduk }}"
                                                    {{ request('namabarang') == $item->namaproduk ? 'selected' : '' }}>
                                                    {{ $item->namaproduk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" name="submit" class="btn btn-sm btn-primary text-white"
                                            style="margin-top:30px">Cari</button>
                                        <a target="_blank"
                                            href="{{ url('admin/laporanpenjualancetak', ['tahun' => request('tahun', date('Y')), 'bulan' => request('bulan', date('m')), 'namabarang' => request('namabarang') ?? 'all']) }}"
                                            class="btn btn-sm btn-success text-white" style="margin-top:30px">PDF</a>
                                        <a target="_blank"
                                            href="{{ url('admin/laporanpenjualanexcel', ['tahun' => request('tahun', date('Y')), 'bulan' => request('bulan', date('m')), 'namabarang' => request('namabarang') ?? 'all']) }}"
                                            class="btn btn-sm btn-success text-white" style="margin-top:30px">Excel</a>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table" id="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No. Nota</th>
                                            <th>Tanggal Penjualan</th>
                                            <th>Nama Produk</th>
                                            <th>Harga</th>
                                            <th>Jumlah</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $nomor = 1; @endphp
                                        @foreach ($penjualan as $item)
                                            <tr>
                                                <td>{{ $nomor++ }}</td>
                                                <td>{{ $item->kodenota }}</td>
                                                <td>{{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}</td>
                                                <td>{{ $item->namabarang }}</td>
                                                <td>{{ rupiah($item->harga) }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ rupiah($item->total) }}</td>
                                                <td>{{ $item->status }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
