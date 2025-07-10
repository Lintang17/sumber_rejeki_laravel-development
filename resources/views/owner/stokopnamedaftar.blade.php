{{-- @extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title">Daftar Barang untuk Stok Opname</h4>
                    <table class="table table-bordered" id="tabelStokOpname">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Stok</th>
                                {{-- <th>Stok Tambahan</th> --}}
                                <th>Stok Terjual</th>
                                <th>Stok Sisa</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                            <tr>
                                <td>{{ $item->produksi->namaproduk ?? 'Tidak ditemukan' }}</td>
                                <td>{{ $item->stok_awal }}</td>
                                {{-- <td>{{ $item->stok_tambahan }}</td> --}}
                                <td>{{ $item->stok_terjual }}</td>
                                <td>{{ $item->stok_sisa }}</td>
                                {{-- 
                                <td>
                                    <a href="{{ route('stokopname.tambah', $item->idshowroom) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-square"></i> Proses Opname
                                    </a> 
                                </td> 
                                --}}
                            {{-- </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#tabelStokOpname').DataTable({
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
--}}
