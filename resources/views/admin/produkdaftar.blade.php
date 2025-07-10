@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Barang</h4>

                        <style>
                            .judul-bold {
                                font-weight: bold !important;
                            }
                        </style>

                        <div class="table-responsive">
                            <table class="table" id="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Stok</th>
                                        <th>Harga Jual</th>
                                        <th>Foto</th>
                                        <th>Barcode</th>
                                        <th>Cetak Barcode</th>
                                        @if (auth()->user()->role == 'Admin')
                                            <th>Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($produk as $key => $value)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $value->namaproduk }}</td>
                                            <td>{{ $value->stok }}</td>
                                            <td>{{ rupiah($value->hargajual) }}</td>
                                            <td>
                                                <button type="button" class="btn btn-link p-0" data-toggle="modal"
                                                    data-target="#modalFoto{{ $value->idproduk }}">
                                                    <img src="{{ asset('assets/foto/' . $value->fotoproduk) }}" width="100px" style="object-fit: cover;">
                                                </button>
                                            </td>
                                            <td>
                                                @php
                                                    $barcodePath = asset('assets/foto/qr/' . $value->barcode . '.png');
                                                @endphp
                                                @if (file_exists(public_path('assets/foto/qr/' . $value->barcode . '.png')))
                                                    <button type="button" class="btn btn-link p-0" data-toggle="modal"
                                                        data-target="#modalQR{{ $value->idproduk }}">
                                                        <img src="{{ $barcodePath }}" alt="Barcode" width="100"
                                                            height="100">
                                                    </button>
                                                @else
                                                    <p>Barcode tidak tersedia</p>
                                                @endif
                                            </td>
                                            <td>
                                                @if (file_exists(public_path('assets/foto/qr/' . $value->barcode . '.png')))
                                                    <a href="{{ $barcodePath }}" target="_blank" class="btn btn-primary"
                                                        download>Cetak Barcode</a>
                                                @else
                                                    <p>Barcode tidak tersedia</p>
                                                @endif
                                            </td>
                                            @if (auth()->user()->role == 'Admin')
                                                <td>
                                                    <a href="{{ url('admin/produkedit', $value->idproduk) }}"
                                                        class="btn btn-success m-1">Edit</a>
                                                    <a href="{{ url('admin/produkhapus', $value->idproduk) }}"
                                                        class="btn btn-danger m-1"
                                                        onclick="return confirm('Yakin Mau di Hapus?')">Hapus</a>
                                                </td>
                                            @endif
                                        </tr>

                                        <!-- Modal untuk Foto Produk -->
                                        <div class="modal fade" id="modalFoto{{ $value->idproduk }}" tabindex="-1"
                                            role="dialog" aria-labelledby="modalFotoLabel{{ $value->idproduk }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalFotoLabel{{ $value->idproduk }}">
                                                            Foto Produk - {{ $value->namaproduk }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ asset('assets/foto/' . $value->fotoproduk) }}" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal untuk QR -->
                                        <div class="modal fade" id="modalQR{{ $value->idproduk }}" tabindex="-1"
                                            role="dialog" aria-labelledby="modalQRLabel{{ $value->idproduk }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalQRLabel{{ $value->idproduk }}">
                                                            Barcode - {{ $value->namaproduk }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="{{ $barcodePath }}" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
