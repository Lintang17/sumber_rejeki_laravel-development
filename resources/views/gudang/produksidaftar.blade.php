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
                                            <div style="white-space: nowrap;">
                                                @if($item->status == 'Menunggu')
                                                    {{-- Edit --}} 
                                                    <div style="margin-bottom: 6px;">
                                                    <a href="{{ url('gudang/produksiedit/' . $item->idproduksi) }}"
                                                        class="btn btn-sm text-white"
                                                        style="background-color: #6f42c1; min-width: 93px;"
                                                        data-toggle="tooltip" title="Edit Produksi">
                                                        <i class="bi bi-pencil-fill me-1"></i> Edit
                                                    </a>
                                                    </div>
                                                @else
                                                    <a href="{{ url('gudang/produksitambahjumlah/' . $item->idproduksi) }}"
                                                        class="btn btn-sm btn-success"
                                                        data-toggle="tooltip" title="Tambah Jumlah">
                                                        <i class="bi bi-plus-circle me-1"></i> Tambah
                                                    </a>
                                                @endif
                                                {{-- Hapus --}}
                                                <form action="{{ route('produksi.hapus', $item->idproduksi) }}"
                                                        method="POST"
                                                        class="form-hapus-produksi m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        style="min-width: 93px;"
                                                        onclick="konfirmasiHapus(this)" 
                                                        data-toggle="tooltip" title="Hapus Produksi">
                                                        <i class="bi bi-trash-fill me-1"></i> Hapus 
                                                    </button>
                                                </form>
                                            </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // DataTables
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
         
        // Tooltip 
        $('[data-toggle="tooltip"]').tooltip(); 
    }); 
    
    // KONFIRMASI HAPUS PRODUKSI
    function konfirmasiHapus(button) {
        const form = button.closest('form'); 
        Swal.fire({ 
            title: 'Apakah Anda yakin?', 
            text: 'Data produksi yang dihapus tidak dapat dikembalikan!', 
            icon: 'warning', 
            showCancelButton: true, 
            confirmButtonColor: '#d33', 
            cancelButtonColor: '#6c757d', 
            confirmButtonText: 'Ya, Hapus', 
            cancelButtonText: 'Tidak', 
            reverseButtons: true
        }).then(function (result) { 
            
            // Jika klik Ya, Hapus
            if (result.isConfirmed) { 
                form.submit(); 
            }
         });
    }
</script> 
@endsection