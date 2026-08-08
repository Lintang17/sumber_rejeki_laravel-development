@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center"> 
                    <h4 class="mb-0 font-weight-bold"> Data Produksi Barang </h4> 
                    <a href="{{ url('gudang/produksitambah') }}" class="btn text-white" style="background-color: #6f42c1;">
                        <i class="bi bi-plus-circle mr-2"></i> Tambah Produksi Barang 
                    </a> 
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table id="table" class="table table-bordered table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Nama Produk</th>
                                    <th>Stok Awal</th>
                                    <th>Stok Tambahan</th>
                                    <th>Stok Total</th>
                                    <th>HPP Estimasi</th>
                                    <th>HPP Final</th>
                                    <th>Harga Jual</th>
                                    <th>Deskripsi</th>
                                    <th>Foto</th>
                                    <th>Status</th>
                                    <th>Tanggal Update</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produksi as $index => $item)
                                    <tr @if($item->status == 'Menunggu' && $item->stok_tambahan > 0) class="table-warning" @endif>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y') }}</td>
                                        <td>{{ $item->namaproduk }}</td>
                                        <td>{{ is_numeric($item->stok_awal) ? $item->stok_awal : '-' }}</td>
                                        <td>{{ is_numeric($item->stok_tambahan) ? $item->stok_tambahan : '-' }}</td>
                                        <td>{{ is_numeric($item->stok) ? $item->stok : '-' }}</td>
                                        <td>Rp {{ number_format($item->hppestimasi ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $item->hppfinal ? 'Rp ' . number_format($item->hppfinal, 0, ',', '.') : '-' }}</td>
                                        <td>{{ $item->hargajual ? 'Rp ' . number_format($item->hargajual, 0, ',', '.') : '-' }}</td>
                                        <td>{{ $item->deskripsiproduk ?? '-' }}</td>
                                        <td>
                                            @if($item->fotoproduk)
                                                <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#fotoModal{{ $item->idproduksi }}">
                                                    <img src="{{ asset('uploads/foto_produk/' . $item->fotoproduk) }}" alt="Foto Produk" width="80" height="80">
                                                </button>
                                            @else
                                                <span class="text-muted">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($item->status == 'Menunggu') badge-warning
                                                @elseif($item->status == 'Selesai') badge-primary
                                                @else badge-secondary @endif">
                                                {{ $item->status }}
                                            </span>
                                            @if($item->status == 'Menunggu' && $item->stok_tambahan > 0)
                                                <br><small class="text-danger font-italic">(Menunggu review ulang)</small>
                                            @elseif($item->status == 'Menunggu' && $item->stok_tambahan == 0)
                                                <br><small class="text-muted font-italic">(Belum direview)</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->tanggal_update ? \Carbon\Carbon::parse($item->tanggal_update)->format('d-m-Y H:i') : '-' }}</td>
                                        <td>{{ $item->tanggalselesai ? \Carbon\Carbon::parse($item->tanggalselesai)->format('d-m-Y H:i') : '-' }}</td>
                                        <td>
                                            @if($item->status == 'Menunggu')
                                                <a href="{{ url('gudang/produksiedit/' . $item->idproduksi) }}"
                                                    class="btn btn-sm text-white"
                                                    style="background-color: #6f42c1;"
                                                    data-toggle="tooltip" title="Edit Produksi">
                                                    <i class="bi bi-pencil-fill me-1"></i> Edit
                                                </a>
                                            @else
                                                <a href="{{ url('gudang/produksitambahjumlah/' . $item->idproduksi) }}"
                                                    class="btn btn-sm btn-success"
                                                    data-toggle="tooltip" title="Tambah Jumlah">
                                                    <i class="bi bi-plus-circle"></i> Tambah
                                                </a>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Modal Zoom Foto --}}
                                    @if($item->fotoproduk)
                                    <div class="modal fade" id="fotoModal{{ $item->idproduksi }}" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel{{ $item->idproduksi }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Foto Produk - {{ $item->namaproduk }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('uploads/foto_produk/' . $item->fotoproduk) }}" class="img-fluid rounded" style="max-height: 500px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">Belum ada data produksi</td>
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
        $('#table').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "zeroRecords": "Tidak ditemukan data yang cocok",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(disaring dari _MAX_ total entri)"
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush

@push('styles')
<style>
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
    }
    th, td {
        vertical-align: middle !important;
    }
    .table-warning {
        background-color: #fff3cd !important;
    }
</style>
@endpush
