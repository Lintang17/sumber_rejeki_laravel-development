
@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">

                    <div class="text-center mb-4">
                        <h3 class="font-weight-bold">Laporan Penjualan Gudang</h3>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('laporan.gudang.penjualan.pdf') }}"
                           class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                            Download PDF
                        </a>

                        <a href="{{ route('laporan.gudang.penjualan.excel') }}"
                           class="btn btn-success">
                            <i class="bi bi-file-earmark-excel-fill"></i>
                            Download Excel
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Penjualan</th>
                                    <th>No Nota</th>
                                    <th>Nama Produk</th>
                                    <th>Jumlah Terjual</th>
                                    <th>Harga Satuan</th>
                                    <th>Total Penjualan</th>
                                    <th>Status Pengiriman</th>
                                </tr>
                            </thead>

                            <tbody>
                                @php $nomor = 1; @endphp

                                @foreach ($data as $item)
                                    @foreach ($item->penjualandetail as $detail)
                                        <tr>
                                            <td>{{ $nomor++ }}</td>
                                            <td>
                                                {{ $item->tanggalpenjualan
                                                    ? \Carbon\Carbon::parse($item->tanggalpenjualan)->format('d-m-Y')
                                                    : '-' }}
                                            </td>
                                            <td>{{ $item->notajual ?? '-' }}</td>
                                            <td>{{ $detail->namaproduk ?? '-' }}</td>
                                            <td>{{ $detail->jumlah_pembelian ?? 0 }}</td>
                                            <td>
                                                Rp {{ number_format($detail->harga ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                Rp {{ number_format($detail->total ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td>{{ $item->statuspengiriman ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                @endforeach

                                @if ($nomor === 1)
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Belum ada data penjualan.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#table').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ditemukan data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 dari 0 entri",
                infoFiltered: "(difilter dari _MAX_ total entri)"
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100]
        });
    });
</script>
@endpush