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
                                        <th class="text-center">Tanggal Penjualan</th>
                                        <th width="30%" class="text-center">Daftar Produk</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Diskon</th>
                                        <th class="text-center">Total Belanja</th>
                                        <th class="text-center">Metode Pembayaran</th>
                                        {{-- <th class="text-center">Status Pembayaran</th> --}}
                                        {{-- @if (auth()->user()->role != 'Kasir')
                                            <th class="text-center">Status Pengiriman</th>
                                        @endif
                                        @if (auth()->user()->role != 'Owner')
                                            <th class="text-center">Aksi</th>
                                        @endif
                                        --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $nomor = 1;
                                        $totalpemasukan = 0;
                                    @endphp
                                    @foreach ($penjualan as $item)
                                        <tr>
                                            <td>{{ $nomor++ }}</td>
                                            <td>{{ $item->notajual }}</td>
                                            <td>{{ date('d-m-Y', strtotime($item->tanggalpenjualan)) }}</td>
                                            <td width="40%">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <th>Produk</th>
                                                        <th>Jumlah</th>
                                                        <th>Harga</th>
                                                    </tr>
                                                    @if (!empty($item->penjualandetail) && $item->penjualandetail->count() > 0)
                                                        @foreach ($item->penjualandetail as $detail)
                                                            <tr>
                                                                <td width="40%">{{ $detail->namabarang }}</td>
                                                                <td width="10%">{{ $detail->jumlah }}</td>
                                                                <td width="30%">{{ rupiah($detail->harga) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                                        </tr>
                                                    @endif

                                                </table>
                                            </td>
                                            @php
                                                $total_sebelum_diskon =
                                                    $item->diskon > 0
                                                        ? $item->grandtotal / (1 - $item->diskon / 100)
                                                        : $item->grandtotal;
                                                $totalpemasukan += $item->grandtotal;
                                            @endphp
                                            <td>{{ rupiah($total_sebelum_diskon) }}</td>
                                            <td>{{ $item->diskon }} %</td>
                                            <td>{{ rupiah($item->grandtotal) }}</td>
                                            <td>{{ $item->metodepembayaran }}</td>
                                            {{-- <td>
                                                @if ($item->statuspembayaran == 'Lunas')
                                                    <button class="btn btn-success">Lunas</button>
                                                @else
                                                    <button class="btn btn-danger">Belum Lunas</button>
                                                @endif
                                            </td> --}}
                                            {{-- @if (auth()->user()->role != 'Kasir')
                                                <td>
                                                    <button class="btn btn-warning text-white" data-toggle="modal"
                                                        data-target="#ubahStatus{{ $item->idpenjualan }}">
                                                        {{ $item->statustransaksi }}
                                                    </button>  
                                                </td>
                                            @endif --}}
                                            @if (auth()->user()->role != 'Owner')
                                                <td>
                                                    <button class="btn btn-success text-white mb-1" data-toggle="modal"
                                                        data-target="#detail{{ $item->notajual }}">Detail</button>
                                                    <a class="btn btn-info text-white mb-1"
                                                        href="{{ url('owner/cetakfaktur', $item->notajual) }}"
                                                        target="_blank">Nota</a>
                                                    {{-- <a href="{{ url('owner/barangkeluarhapus', $item->notajual) }}"
                                                        class="btn btn-danger mb-1"
                                                        onclick="return confirm('Yakin Mau di Hapus?')">Hapus</a> --}}
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right"><em>Total Pemasukan :</em></th>
                                        <th colspan="3">{{ rupiah($totalpemasukan) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @foreach ($penjualan as $key => $item)
                            <!-- Modal Detail -->
                            <div class="modal fade" id="detail{{ $item->notajual }}" tabindex="-1" role="dialog"
                                aria-labelledby="modalLabel{{ $item->notajual }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Penjualan - Nota: {{ $item->notajual }}</h5>
                                            <button type="button" class="close"
                                                data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Barang</th>
                                                        <th>Harga</th>
                                                        <th>Jumlah</th>
                                                        <th>Total Harga</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $no = 1; @endphp
                                                    @if ($item->penjualandetail && $item->penjualandetail->count() > 0)
                                                        @foreach ($item->penjualandetail as $detail)
                                                            <tr>
                                                                <td>{{ $no++ }}</td>
                                                                <td>{{ $detail->namabarang }}</td>
                                                                <td>{{ rupiah($detail->harga) }}</td>
                                                                <td>{{ $detail->jumlah }}</td>
                                                                <td>{{ rupiah($detail->total) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Ubah Status
                                                        <div class="modal fade" id="ubahStatus{{ $item->idpenjualan }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Ubah Status Pengiriman</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><span>&times;</span></button>
                                                                    </div>
                                                                    <form action="{{ url('owner/ubahstatustransaksi') }}" method="POST">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="notajual" value="{{ $item->notajual }}">
                                                                            <select class="form-control" name="statustransaksi">
                                                                                <option value="Dikirim"
                                                                                    {{ $item->statustransaksi == 'Dikirim' ? 'selected' : '' }}>
                                                                                    Dikirim</option>
                                                                                <option value="Selesai"
                                                                                    {{ $item->statustransaksi == 'Selesai' ? 'selected' : '' }}>
                                                                                    Selesai</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Batal</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div> -->
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
