@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Produksi Selesai</h4>

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
                                    <th>Nama Produk</th>
                                    <th>Tanggal Produksi</th>
                                    <th>Stok</th>
                                    <th>HPP Final</th>
                                    <th>Harga Jual</th>
                                    <th>Foto</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($produksi as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->namaproduk }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y') }}</td>
                                        <td>{{ $item->stok }}</td>
                                        <td>
                                            {{ $item->hppfinal ? 'Rp ' . number_format($item->hppfinal, 0, ',', '.') : '-' }}
                                        </td>
                                        <td>
                                            {{ $item->hargajual ? 'Rp ' . number_format($item->hargajual, 0, ',', '.') : '-' }}
                                        </td>
                                        <td>
                                            @if($item->fotoproduk)
                                                <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#fotoModal{{ $item->idproduksi }}">
                                                    <img src="{{ asset('uploads/foto_produk/' . $item->fotoproduk) }}" alt="Gambar Produk" width="80" height="80">
                                                </button>
                                            @else
                                                <span class="text-muted">Tidak ada gambar</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->tanggalselesai ? \Carbon\Carbon::parse($item->tanggalselesai)->format('d-m-Y H:i') : '-' }}
                                        </td>
                                    </tr>

                                    {{-- Modal Zoom Foto --}}
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
                                        <td colspan="8" class="text-center">Tidak ada data produksi selesai</td>
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
                "zeroRecords": "Tidak ditemukan data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 dari 0 entri",
                "infoFiltered": "(difilter dari _MAX_ total entri)"
            }
        });
    });
</script>
@endpush
