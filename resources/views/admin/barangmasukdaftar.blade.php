@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Barang Masuk</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table">
                            <thead class="thead-dark">
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>No. Nota</th>
                                    <th>Tanggal Barang Masuk</th>
                                    <th>Daftar Barang</th>
                                    <th>Total Belanja</th>
                                    @if (auth()->user()->role == 'Admin')
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $nomor = 1;
                                    $totalpengeluaran = 0;
                                @endphp
                                @foreach ($pembelian as $item)
                                    <tr class="text-center">
                                        <td>{{ $nomor++ }}</td>
                                        <td>{{ $item->notabeli }}</td>
                                        <td>{{ date('d-m-Y', strtotime($item->tanggalpembelian)) }}</td>
                                        <td class="text-left">
                                            <table class="table table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th>Jumlah</th>
                                                        <th>HPP</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pembeliandetail[$item->notabeli] as $barang)
                                                        <tr>
                                                            <td>{{ $barang->namabarang }}</td>
                                                            <td>{{ $barang->jumlah }}</td>
                                                            <td>{{ number_format($barang->harga) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                        <td>{{ number_format($item->grandtotal) }}</td>

                                        @if (auth()->user()->role == 'Admin')
                                            <td>
                                                <a href="{{ url('admin/barangmasukedit/'. $item->notabeli) }}"
                                                    class="btn btn-warning btn-sm m-1">Edit</a>

                                                <a href="{{ url('admin/barangmasukhapus/'. $item->notabeli) }}"
                                                    class="btn btn-danger btn-sm m-1"
                                                    onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                            </td>
                                        @endif
                                    </tr>
                                    @php $totalpengeluaran += $item->grandtotal; @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-right">Total Pengeluaran :</th>
                                    <th>{{ number_format($totalpengeluaran) }}</th>
                                    @if (auth()->user()->role == 'Admin')
                                        <th></th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Modal status pembelian --}}
                    @php $no = 1; @endphp
                    @foreach ($pembelian as $item)
                        <div class="modal fade" id="status{{ $no }}" tabindex="-1" role="dialog">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Status Pembelian</h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <form method="post" action="{{ url('admin/barangmasuk/update-status') }}">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="notabeli" value="{{ $item->notabeli }}">
                                            <div class="form-group">
                                                <label>Status Pembelian</label>
                                                <select class="form-control" name="statuspembelian" required>
                                                    <option value="Belum Di Proses" {{ $item->statuspembelian == 'Belum Di Proses' ? 'selected' : '' }}>Belum Di Proses</option>
                                                    <option value="Proses Pengiriman" {{ $item->statuspembelian == 'Proses Pengiriman' ? 'selected' : '' }}>Proses Pengiriman</option>
                                                    <option value="Sudah Di Terima" {{ $item->statuspembelian == 'Sudah Di Terima' ? 'selected' : '' }}>Sudah Di Terima</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @php $no++; @endphp
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    @once
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    @endonce

    <style>
        table.table-sm th, table.table-sm td {
            padding: 6px 8px !important;
            font-size: 13px !important;
        }
    </style>

    <script>
        $(document).ready(function () {
            if (!$.fn.DataTable.isDataTable('#table')) {
                $('#table').DataTable({
                    pageLength: 5,
                    responsive: true,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "›",
                            previous: "‹"
                        },
                        zeroRecords: "Data tidak ditemukan",
                        emptyTable: "Tidak ada data tersedia"
                    }
                });
            }
        });
    </script>
@endsection
