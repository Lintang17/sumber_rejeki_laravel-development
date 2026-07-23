@extends('layouts.admin')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Penjualan</h4>
                        <div class="table-responsive">
                            <table class="table" id="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">No. Nota</th>
                                        <th class="text-center">Tanggal</th>
                                        <th class="text-center">Nama Pembeli</th>
                                        <th class="text-center">No. Telp</th>
                                        <th class="text-center">Alamat</th>
                                        <th width="30%" class="text-center">Daftar Produk</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">DP</th>
                                        <th class="text-center">Uang Pembeli</th>
                                        <th class="text-center">Sisa Bayar</th>
                                        <th class="text-center">Kembalian</th>
                                        <th class="text-center">Metode</th>
                                        @if (auth()->user()->role != 'Kasir')
                                            <th class="text-center">Status</th>
                                        @endif
                                        @if (auth()->user()->role != 'Owner')
                                            <th class="text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $nomor = 1;
                                        $totalpemasukan = 0;
                                    @endphp
                                    @foreach ($penjualan as $item)
                                        @php
                                            $totalpemasukan += ($item->dp ?? 0) + ($item->sisabayar ?? 0);
                                            $belumLunas = ($item->sisabayar ?? 0) > 0;
                                        @endphp
                                        <tr class="{{ $belumLunas ? 'table-warning' : '' }}">
                                            <td class="text-center">{{ $nomor++ }}</td>
                                            <td class="text-center">{{ $item->notajual }}</td>
                                            <td class="text-center">{{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}</td>
                                            <td class="text-center">{{ $item->namapembeli ?? '-' }}</td>
                                            <td class="text-center">{{ $item->notelp ?? '-' }}</td>
                                            <td class="text-center">{{ $item->alamat ?? '-' }}</td>
                                            <td>
                                                <table style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th>Nama Produk</th>
                                                            <th>Jumlah</th>
                                                            <th>Harga</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @if ($item->penjualandetail && $item->penjualandetail->count())
                                                            @foreach ($item->penjualandetail as $detail)
                                                                <tr>
                                                                    <td>{{ $detail->namaproduk }}</td>
                                                                    <td>{{ $detail->jumlah_pembelian }}</td>
                                                                    <td>{{ rupiah($detail->harga) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="3" class="text-center">Tidak ada data</td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td class="text-center">{{ rupiah($item->grandtotal) }}</td>
                                            <td class="text-center">{{ rupiah($item->dp ?? 0) }}</td>
                                            <td class="text-center">{{ rupiah($item->bayar ?? 0) }}</td>
                                            <td class="text-center">{{ rupiah($item->sisabayar ?? 0) }}</td>
                                            <td class="text-center">{{ rupiah($item->kembali ?? 0) }}</td>
                                            <td class="text-center">{{ $item->metodepembayaran }}</td>

                                            @if (auth()->user()->role != 'Kasir')
                                                <td class="text-center">
                                                    <button class="btn btn-warning text-white" data-toggle="modal" data-target="#ubahStatus{{ $item->idpenjualan }}">
                                                        {{ $item->statustransaksi }}
                                                    </button>
                                                </td>
                                            @endif

                                            @if (auth()->user()->role != 'Owner')
                                                <td class="text-center align-middle" style="width:240px;">
                                                    <div class="btn-group" role="group">
                                                        <button type="button" data-toggle="modal" data-target="#detail{{ $item->notajual }}" class="btn text-white"
                                                            style="background:#8B5E5E; border:none; min-width:75px; height:42px; font-size:13px; font-weight:600; border-radius:8px 0 0 8px;">
                                                            Detail
                                                        </button>
                                                        <a href="{{ url('kasir/cetakfaktur',$item->notajual) }}" target="_blank" class="btn text-white" 
                                                            style="background:#D89A2B; border:none; min-width:75px; height:42px; font-size:13px; font-weight:600;">
                                                            Faktur
                                                        </a>
                                                        <a href="{{ url('kasir/cetaknota',$item->notajual) }}" target="_blank" class="btn text-white"
                                                            style="background:#2F8F83; border:none; min-width:75px; height:42px; font-size:13px; font-weight:600; border-radius:0 8px 8px 0;">
                                                            Nota
                                                        </a>
                                                    </div>
                                                    {{-- <a href="{{ url('kasir/barangkeluarhapus', $item->notajual) }}" class="btn btn-danger mb-1" onclick="return confirm('Yakin Mau di Hapus?')">Hapus</a> --}}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-right"><em>Total Pemasukan:</em></th>
                                        <th colspan="5">{{ rupiah($totalpemasukan) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        {{-- MODAL --}}
                        @foreach ($penjualan as $item)
                            <div class="modal fade" id="detail{{ $item->notajual }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Penjualan - Nota: {{ $item->notajual }}</h5>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Produk</th>
                                                        <th>Harga</th>
                                                        <th>Jumlah</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @foreach ($item->penjualandetail as $detail)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $detail->namaproduk }}</td>
                                                            <td>{{ rupiah($detail->harga) }}</td>
                                                            <td>{{ $detail->jumlah_pembelian }}</td>
                                                            <td>{{ rupiah($detail->total) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="ubahStatus{{ $item->idpenjualan }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ url('kasir/ubahstatustransaksi') }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Ubah Status Pengiriman</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="notajual" value="{{ $item->notajual }}">
                                                <select class="form-control" name="statustransaksi" required>
                                                    <option value="Dikirim" {{ $item->statustransaksi == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                                                    <option value="Selesai" {{ $item->statustransaksi == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
