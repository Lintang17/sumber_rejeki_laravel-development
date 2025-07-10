@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-lg-12 stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title">Riwayat Hasil Stok Opname</h4>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Tanggal Opname</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Fisik</th>
                                    <th>Selisih</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->showroom->produksi->namaproduk ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_opname)->format('d-m-Y') }}</td>
                                        <td>{{ $item->stok_sistem }}</td>
                                        <td>{{ $item->stok_fisik }}</td>
                                        <td class="{{ $item->selisih < 0 ? 'text-danger' : ($item->selisih > 0 ? 'text-success' : '') }}">
                                            {{ $item->selisih }}
                                        </td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Belum ada data stok opname.</td>
                                    </tr>
                                @endforelse
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

