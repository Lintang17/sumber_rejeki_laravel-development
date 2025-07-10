@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Barang Showroom</h4>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Stok</th>
                                    {{-- <th>Stok Tambahan</th>--}}
                                    <th>Stok Terjual</th>
                                    <th>Sisa Stok</th>
                                    <th>Harga Jual</th>
                                    <th>Foto</th>
                                    {{-- <th>Barcode</th> --}}
                                    <th>Tanggal Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($showroom as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->produksi->namaproduk ?? 'Data hilang' }}</td>
                                        <td>{{ $item->stok_awal ?? 0 }}</td>
                                        {{--<td>{{ $item->stok_tambahan ?? 0 }}</td>--}}
                                        <td>{{ $item->stok_terjual ?? 0 }}</td>
                                        <td>{{ $item->stok_sisa ?? 0 }}</td>
                                        <td>
                                            @if($item->produksi)
                                                Rp {{ number_format($item->produksi->hargajual, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->produksi && $item->produksi->fotoproduk)
                                                <button type="button" class="btn btn-link p-0" data-toggle="modal" data-target="#fotoModal{{ $item->idshowroom }}">
                                                    <img src="{{ asset('uploads/foto_produk/' . $item->produksi->fotoproduk) }}" width="80" height="80" alt="Foto Produk">
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        {{-- <td class="text-center">
                                            @if($item->kodeqr)
                                                <a href="{{ asset('assets/foto/qr/' . $item->kodeqr) }}" target="_blank">
                                                    <img src="{{ asset('assets/foto/qr/' . $item->kodeqr) }}" width="80" alt="Barcode">
                                                </a>
                                                <br>
                                                <a href="{{ asset('assets/foto/qr/' . $item->kodeqr) }}" download class="btn btn-sm btn-primary mt-2">Download PNG</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td> --}}
                                        <td>{{ \Carbon\Carbon::parse($item->tanggalmasuk)->format('d-m-Y') }}</td>
                                    </tr>

                                    {{-- Modal Zoom Foto --}}
                                    @if($item->produksi && $item->produksi->fotoproduk)
                                    <div class="modal fade" id="fotoModal{{ $item->idshowroom }}" tabindex="-1" role="dialog" aria-labelledby="fotoModalLabel{{ $item->idshowroom }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="fotoModalLabel{{ $item->idshowroom }}">
                                                        Foto Produk - {{ $item->produksi->namaproduk ?? '-' }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="{{ asset('uploads/foto_produk/' . $item->produksi->fotoproduk) }}" class="img-fluid rounded" style="max-height: 500px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada barang masuk showroom.</td>
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
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ditemukan data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 dari 0 entri",
                infoFiltered: "(difilter dari _MAX_ total entri)"
            }
        });
    });
</script>
@endpush
