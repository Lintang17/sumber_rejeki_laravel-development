@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Laporan Pembelian</h4>

                        {{-- Form Filter Tanggal --}}
                        <form method="GET" action="{{ url('admin/laporanpembelian') }}">
                            <div class="row mt-3 mb-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Awal</label>
                                        <input type="date" class="form-control" name="tanggalawal"
                                            value="{{ $tanggalAwal }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="tanggalakhir"
                                            value="{{ $tanggalAkhir }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <button type="submit"
                                            class="btn btn-primary text-white mt-4 btn-block">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- beetween --}}
                        <div class="d-flex justify-content-space">
                            {{-- Form Cetak --}}
                            <form method="POST" action="{{ url('admin/laporanpembeliancetak') }}" target="_blank"
                                class="mr-2">
                                @csrf
                                <input type="hidden" name="tanggalawalfix" value="{{ $tanggalAwal }}">
                                <input type="hidden" name="tanggalakhirfix" value="{{ $tanggalAkhir }}">
                                <button type="submit" class="btn btn-success text-white mt-4" name="cetak"
                                    value="cetak">PDF</button>
                            </form>
                            <form method="POST" action="{{ url('admin/laporanpembelianexcel') }}" target="_blank">
                                @csrf
                                <input type="hidden" name="tanggalawalfix" value="{{ $tanggalAwal }}">
                                <input type="hidden" name="tanggalakhirfix" value="{{ $tanggalAkhir }}">
                                <button type="submit" class="btn btn-info text-white mt-4" name="cetak"
                                    value="excel">Excel</button>
                            </form>

                        </div>

                        {{-- Tabel Laporan --}}
                        <div class="table-responsive">
                            <table class="table" id="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Tanggal Barang Masuk</th>
                                        <th class="text-center">Nama Produk</th>
                                        <th class="text-center">Harga</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $nomor = 1; @endphp
                                    @foreach ($pembelian as $item)
                                        <tr>
                                            <td class="text-center">{{ $nomor++ }}</td>
                                            <td class="text-center">
                                                {{ date('d-m-Y', strtotime($item->tanggalpembelian)) }}</td>
                                            <td class="text-center">{{ $item->namabarang }}</td>
                                            <td class="text-center">{{ number_format($item->harga, 2, ',', '.') }}</td>
                                            <td class="text-center">{{ $item->jumlah }}</td>
                                            <td class="text-center">{{ number_format($item->total, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <th colspan="5" class="text-right"><em>Total Pengeluaran :</em></th>
                                    <th colspan="2">{{ number_format($totalPengeluaran, 2, ',', '.') }}</th>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
