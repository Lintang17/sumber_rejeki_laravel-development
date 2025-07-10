{{--@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <h4 class="mt-4 mb-3">Daftar Produksi yang Menunggu Review</h4>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($produksi->isEmpty())
        <div class="alert alert-info">Tidak ada produksi yang menunggu review.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="table-review">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th>Tanggal Produksi</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produksi as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->namaproduk }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y') }}</td>
                            <td>{{ $item->jumlah }}</td>
                            <td>
                                <a href="{{ url('owner/produksireview/' . $item->idproduksi) }}" class="btn btn-sm btn-primary">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<!-- DataTables (jika belum dimuat dari layout utama) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(document).ready(function () {
        $('#table-review').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ditemukan data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ total entri)"
            }
        });
    });
</script>
@endpush

@push('styles')
<!-- DataTables CSS (jika belum dimuat dari layout utama) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush
--}}