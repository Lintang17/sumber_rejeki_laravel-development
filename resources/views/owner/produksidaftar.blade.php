@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Produksi Barang</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table">
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
                                    <th>Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produksi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y') }}</td>
                                        <td>{{ $item->namaproduk }}</td>
                                        <td>{{ $item->stok_awal ?? '-' }}</td>
                                        <td>{{ $item->stok_tambahan ?? '-' }}</td>
                                        <td>{{ $item->stok }}</td>
                                        <td>Rp {{ number_format($item->hppestimasi, 0, ',', '.') }}</td>
                                        <td>{{ $item->hppfinal ? 'Rp ' . number_format($item->hppfinal, 0, ',', '.') : '-' }}</td>
                                        <td>{{ $item->hargajual ? 'Rp ' . number_format($item->hargajual, 0, ',', '.') : '-' }}</td>
                                        <td>{{ $item->deskripsiproduk ?? '-' }}</td>
                                        <td>
                                            @if($item->fotoproduk)
                                                <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#fotoModal{{ $item->idproduksi }}">
                                                    <img src="{{ asset('uploads/foto_produk/' . $item->fotoproduk) }}" width="80" height="80">
                                                </button>
                                            @else
                                                <span class="text-muted">Tidak ada gambar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($item->status == 'Menunggu') badge-warning
                                                @elseif($item->status == 'Selesai') badge-primary
                                                @elseif($item->status == 'Dibatalkan') badge-danger
                                                @else badge-secondary @endif">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ $item->tanggal_update ? \Carbon\Carbon::parse($item->tanggal_update)->format('d-m-Y H:i') : '-' }}</td>
                                        <td>{{ $item->tanggalselesai ? \Carbon\Carbon::parse($item->tanggalselesai)->format('d-m-Y H:i') : '-' }}</td>
                                        <td>
                                            @if($item->status == 'Menunggu')
                                                <a href="{{ url('owner/produksireview/' . $item->idproduksi) }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="Review Produksi">
                                                    <i class="bi bi-check-circle"></i> Review
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- Modal Foto --}}
                                    @if($item->fotoproduk)
                                    <div class="modal fade" id="fotoModal{{ $item->idproduksi }}" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel{{ $item->idproduksi }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="fotoModalLabel{{ $item->idproduksi }}">Foto Produk - {{ $item->namaproduk }}</h5>
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
                                        <td colspan="15" class="text-center">Tidak ada data produksi</td>
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

@push('styles')
<style>
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
    }
    th, td {
        vertical-align: middle !important;
    }
</style>
@endpush

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
