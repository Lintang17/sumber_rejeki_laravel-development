@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0 font-weight-bold">Purchase Order (Admin)</h4>
                            <small class="text-muted">Kelola semua data purchase order</small>
                        </div>
                    </div>
                    <a href="{{ url('admin/po/tambah') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah PO
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="font-weight-bold text-secondary mb-1">Status</label>
                            <select id="filterStatus" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="Pending">Pending</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Diambil">Diambil</option>
                                <option value="Dikirim">Dikirim</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold text-secondary mb-1">Bulan</label>
                            <input type="month" id="filterMonth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold text-secondary mb-1">Pencarian</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" id="searchPO" class="form-control" placeholder="Cari kode PO atau customer...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="font-weight-bold text-secondary mb-1">&nbsp;</label>
                            <button id="resetFilter" class="btn btn-primary btn-block">
                                <i class="fas fa-undo mr-2"></i> Reset
                            </button>
                        </div>
                    </div>

                    {{-- Info Pembayaran Saat Status Dikirim --}}
                    @php
                        $poMenungguLunas = $po->where('status_pembayaran','!=','Lunas')->count();
                    @endphp
                    @if($poMenungguLunas > 0)
                    <div class="alert alert-warning d-flex align-items-center shadow-sm mb-4" role="alert">
                        <div>
                            <strong>Ada PO yang menunggu pelunasan pembayaran.</strong>
                            <br>
                            Terdapat <strong>{{ $poMenungguLunas }} PO</strong> dengan status 
                            <span class="badge badge-orange">Dikirim</span>
                            Silakan buka 
                            <strong>Lihat Detail</strong> pada data customer untuk mengubah 
                            <strong>Status Pembayaran menjadi Lunas</strong> setelah pembayaran diterima.
                            </div>
                        </div>
                    @endif

                    {{-- Info Pelunasan dan ubah Status menjadi Selesai --}}
                    @php
                        $poSiapSelesai = $po->where('status', 'Dikirim')
                            ->where('status_pembayaran', 'Lunas')
                            ->count();
                    @endphp

                    @if($poSiapSelesai > 0)
                    <div class="alert alert-success shadow-sm mb-4">
                        <strong>Ada {{ $poSiapSelesai }} PO yang siap diselesaikan.</strong>
                        <br>
                        Pembayaran customer sudah <strong>Lunas</strong>.
                        Silakan klik tombol
                        <strong>Selesaikan PO</strong>
                        pada kolom <strong>Aksi</strong> agar status berubah menjadi
                        <strong>Selesai</strong>.
                    </div>
                    @endif

                    @php
                        $poBelumSelesai = $po->where('status','Dikirim')->count();
                    @endphp

                    @if($poBelumSelesai > 0)
                    <div class="alert alert-info shadow-sm mb-4">
                        <strong>
                            Ada {{ $poBelumSelesai }} PO yang sudah dikirim.
                        </strong>
                        <br>
                        Jangan lupa ubah status menjadi 
                        <strong>Selesai</strong>
                        setelah pesanan selesai agar data PO tersimpan dengan lengkap.
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="40">#</th>
                                    <th width="150">Kode PO</th>
                                    <th width="150">Customer</th>
                                    <th width="150">Produk</th>
                                    <th>Deskripsi Produksi</th>
                                    <th class="text-center" width="50">Jml</th>
                                    <th class="text-center" width="100">Qty</th>
                                    <th width="130">Estimasi</th>
                                    <th width="100">Keterangan</th>
                                    <th class="text-right" width="120">HPP Admin</th>
                                    <th class="text-right" width="120">HPP Gudang</th>
                                    <th class="text-right" width="120">Harga Jual</th>
                                    <th class="text-center" width="110">Status</th>
                                    <th class="text-center" width="230">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($po as $index => $item)
                                <tr data-status="{{ $item->status }}" 
                                    data-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m') }}"
                                    data-search="{{ $item->kode_po }} {{ $item->customer }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $item->kode_po }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $item->customer }}</strong>
                                        <br>
                                        <a href="#" class="text-primary" data-toggle="modal" data-target="#customer{{ $item->id }}">
                                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                                        </a>
                                    </td>
                                    <td>
                                        @foreach($item->detail as $detail)
                                            <span class="badge badge-primary mb-1" style="display: block;">{{ $detail->produk }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1">{{ $detail->deskripsi ?? '-' }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $item->detail->count() }}</span>
                                    </td>
                                    <td>
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1 text-center">
                                                <span class="badge badge-secondary" style="font-size: 13px; padding: 5px 15px; min-width: 40px; display: inline-block;">
                                                    {{ $detail->qty }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($item->estimasi_awal)
                                            <small class="text-success">
                                                <i class="fas fa-play mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->estimasi_awal)->format('d/m/Y') }}
                                            </small>
                                            <br>
                                            <small class="text-danger">
                                                <i class="fas fa-stop mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->estimasi_akhir)->format('d/m/Y') }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->keterangan)
                                            <span class="badge badge-warning">{{ $item->keterangan }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1">Rp {{ number_format($detail->hpp_estimasi_admin ?? 0, 0, ',', '.') }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1">Rp {{ number_format($detail->hpp_estimasi_gudang ?? 0, 0, ',', '.') }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1"><strong class="text-success">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</strong></div>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 'Pending')
                                            <span class="badge badge-warning badge-lg">
                                                <i class="fas fa-clock mr-1"></i> Pending
                                            </span>
                                        @elseif($item->status == 'Disetujui')
                                            <span class="badge badge-primary badge-lg">
                                                <i class="fas fa-check-circle mr-1"></i> Disetujui
                                            </span>
                                         @elseif($item->status == 'Diproses')
                                            <span class="badge badge-info badge-lg">
                                                <i class="fas fa-spinner mr-1"></i> Diproses
                                            </span>
                                        @elseif($item->status == 'Diambil')
                                            <span class="badge badge-purple badge-lg">
                                                <i class="fas fa-box mr-1"></i> Diambil
                                            </span>
                                        @elseif($item->status == 'Dikirim')
                                            <span class="badge badge-orange badge-lg">
                                                <i class="fas fa-truck mr-1"></i> Dikirim
                                            </span>
                                        @elseif($item->status == 'Selesai')
                                            <span class="badge badge-success badge-lg">
                                                <i class="fas fa-check-double mr-1"></i> Selesai
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" style="width:100%">
                                            @if($item->status == 'Pending')
                                                <a href="{{ url('admin/po/edit/'.$item->id) }}" 
                                                   class="btn btn-warning">
                                                    <i class="fas fa-edit mr-1"></i> Edit
                                                </a>
                                                <button onclick="hapusPo({{ $item->id }})" 
                                                        class="btn btn-danger">
                                                    <i class="fas fa-trash mr-1"></i> Hapus
                                                </button>
                                            @else
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fas fa-lock mr-1"></i> Terkunci
                                                </button>
                                            @endif
                            
                                            @if($item->status == 'Diambil')
                                                <form id="form-kirim-{{ $item->id }}"
                                                      action="{{ url('admin/po/status/'.$item->id) }}"
                                                       method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden"
                                                            name="status"
                                                            value="Dikirim">
                                                    <button type="button"
                                                            class="btn btn-primary"
                                                            onclick="ubahStatusDikirim({{ $item->id }})">
                                                        Ubah Status
                                                    </button>
                                                </form>
                                            @elseif($item->status == 'Dikirim')
                                                <a href="{{ url('admin/po/print/'.$item->id) }}"
                                                    class="btn btn-info">
                                                    Surat Jalan
                                                </a>
                                                <a href="{{ url('admin/po/print/'.$item->id.'?invoice=true') }}"
                                                    class="btn btn-success">
                                                    Invoice
                                                </a>
                                                @if(strtolower($item->status_pembayaran ?? '') != 'lunas')
                                                    <form id="form-lunas-table-{{ $item->id }}"
                                                        action="{{ url('admin/po/status/'.$item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden"
                                                            name="status_pembayaran"
                                                            value="Lunas">
                                                        <button type="button"
                                                            class="btn btn-warning"
                                                            onclick="konfirmasiLunasTable({{ $item->id }})">
                                                            Lunas
                                                        </button>
                                                    </form>
                                                @else
                                                    <form id="form-selesai-{{ $item->id }}"
                                                        action="{{ url('admin/po/status/'.$item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden"
                                                            name="status"
                                                            value="Selesai">
                                                        <button type="button"
                                                            class="btn btn-success"
                                                            onclick="konfirmasiSelesai({{ $item->id }})">
                                                            Selesaikan PO
                                                        </button>
                                                    </form>
                                                @endif
                                            @elseif($item->status == 'Selesai')
                                                <a href="{{ url('admin/po/print/'.$item->id) }}"
                                                    class="btn btn-success">
                                                    Invoice
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Customer Detail -->
@foreach($po as $item)
<div class="modal fade" id="customer{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    Detail Customer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Kode PO</label>
                            <p class="form-control-static"><strong>{{ $item->kode_po }}</strong></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Tanggal Order</label>
                            <p class="form-control-static">{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Customer</label>
                            <p class="form-control-static"><strong>{{ $item->customer }}</strong></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">No. HP</label>
                            <p class="form-control-static">{{ $item->no_hp ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Alamat</label>
                            <p class="form-control-static">{{ $item->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Foto Produk
                            </label>
                            <div class="d-flex flex-wrap" style="gap:15px;">
                                @foreach($item->detail as $detail)
                                    @if($detail->foto)
                                        <div class="text-center">
                                            <img src="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                                 class="img-thumbnail"
                                                 style="
                                                    width:120px;
                                                    height:120px;
                                                    object-fit:cover;
                                                    border-radius:12px;
                                                 ">
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    {{ $detail->produk }}
                                                </small>
                                             </div>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <div class="d-flex align-items-center justify-content-center"
                                                 style="
                                                    width:120px;
                                                    height:120px;
                                                    background:#f5f5f5;
                                                    border-radius:12px;
                                                    color:#999;
                                                 ">
                                                <i class="fas fa-image fa-2x"></i>
                                            </div>
                                            <small class="text-muted">
                                                {{ $detail->produk }}
                                            </small>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">DP Customer</label>
                            <p class="form-control-static">
                                Rp {{ number_format($item->dp ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Metode Pembayaran</label>
                            <p class="form-control-static">
                                {{ $item->metode_pembayaran ?? '-' }}
                            </p>
                        </div>
                    </div>
                  
                    <div class="mt-3 p-3 rounded border" style="background:#f8f9fa;">
                        <label class="font-weight-bold d-block mb-2">
                            Status Pembayaran
                        </label>
                        @if($item->status_pembayaran == 'DP')
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge badge-warning badge-lg">
                                    DP
                                </span>
                                <span class="text-danger font-weight-bold">
                                    Belum Lunas
                                </span>
                            </div>
                            @if($item->status != 'Selesai')
                                <div class="alert alert-warning mt-3 mb-0 py-2">
                                    <small>
                                        Customer belum melakukan pelunasan.
                                        Silakan konfirmasi setelah pembayaran diterima.
                                    </small>
                                </div>
                                <form id="form-lunas-{{ $item->id }}"
                                      action="{{ url('admin/po/status/'.$item->id) }}"
                                      method="POST"
                                      class="mt-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden"
                                            name="status_pembayaran"
                                            value="Lunas">
                                    
                                    <button type="button"
                                            onclick="konfirmasiLunas({{ $item->id }})"
                                            class="btn btn-success btn-sm btn-block">
                                        Konfirmasi Pembayaran Lunas
                                    </button>
                                </form>
                            @endif
                        @elseif($item->status_pembayaran == 'Lunas')
                            <div class="d-flex align-items-center">
                                <span class="badge badge-success badge-lg">
                                    Lunas
                                </span>
                                <small class="text-success ml-2">
                                    Pembayaran sudah diterima
                                </small>
                            </div>
                        @endif
                    </div>

                    @php
                        $hargaBelumAda = $item->detail->contains(function($d){
                            return empty($d->harga_jual) || $d->harga_jual <= 0;
                        });
                        $totalHargaJual = $item->detail->sum(function($d){
                            return $d->qty * ($d->harga_jual ?? 0);
                        });
                        $sisa = $item->status_pembayaran == 'Lunas'
                            ? 0
                            : max($totalHargaJual - ($item->dp ?? 0), 0);
                    @endphp

                    <div class="mt-3 p-3 rounded" style="background:#fff8e1;">
                        <label class="font-weight-bold mb-1">
                            Total Harga Jual
                        </label>
                        @if($hargaBelumAda)
                            <p class="mb-0 text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Menunggu Owner mengisi harga jual
                            </p>
                        @else
                            <p class="mb-0">
                                <strong class="text-success">
                                    Rp {{ number_format($totalHargaJual,0,',','.') }}
                                </strong>
                            </p>
                        @endif
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-8">
                            <div class="p-3 rounded h-100" style="background:#f8f9fa;">
                                <label class="font-weight-bold mb-1">
                                    Sisa Pembayaran
                                </label>
                        @if($hargaBelumAda)
                            <p class="mb-0 text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Menunggu Owner mengisi harga jual
                            </p>
                        @else
                            <p class="mb-0">
                                <strong class="{{ $sisa <= 0 ? 'text-success' : 'text-danger' }}">
                                    Rp {{ number_format($sisa, 0, ',', '.') }}
                                </strong>
                            </p>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded h-100" style="background:#f8f9fa;">
                        <label class="font-weight-bold mb-2">
                            Status
                        </label>  
                        <p class="mb-0">                    
                            @if($item->status == 'Pending')
                                <span class="badge badge-warning badge-lg">
                                    Pending
                                </span>
                            @elseif($item->status == 'Disetujui')
                                <span class="badge badge-primary badge-lg">
                                    Disetujui
                                </span>
                            @elseif($item->status == 'Diproses')
                                <span class="badge badge-info badge-lg">
                                    Diproses
                                </span>
                            @elseif($item->status == 'Diambil')
                                <span class="badge badge-secondary badge-lg">
                                    Diambil
                                </span>
                            @elseif($item->status == 'Dikirim')
                                <span class="badge badge-dark badge-lg">
                                    Dikirim
                                </span>
                            @elseif($item->status == 'Selesai')
                                <span class="badge badge-success badge-lg">
                                    Selesai
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Form Delete -->
@foreach($po as $item)
<form id="delete-form-{{ $item->id }}" 
      action="{{ url('admin/po/hapus/'.$item->id) }}" 
      method="POST" 
      style="display:none">
    @csrf
    @method('DELETE')
</form>
@endforeach

<style>
    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .card-header {
        border-bottom: 1px solid #e9ecef;
        padding: 20px 25px;
    }
    .card-body {
        padding: 25px;
    }
    .bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    .form-control {
        border-radius: 4px;
        border: 1px solid #ced4da;
        height: 38px;
        font-size: 14px;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102,126,234,0.25);
    }
    select.form-control {
        -webkit-appearance: auto;
        -moz-appearance: auto;
        appearance: auto;
        padding: 6px 12px;
    }
    select.form-control option {
        padding: 8px 12px;
        font-size: 14px;
    }
    .input-group-text {
        border: 1px solid #ced4da;
        border-right: none;
        background: white;
    }
    .input-group .form-control {
        border-left: none;
    }
    .input-group .form-control:focus {
        border-left: none;
    }
    .table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 8px;
        color: #495057;
    }
    .table td {
        font-size: 13px;
        vertical-align: middle;
        padding: 10px 8px;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 4px;
    }
    .badge-lg {
        font-size: 13px;
        padding: 6px 15px;
    }
    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
    .badge-purple{
        background:#7C3AED;
        color:#fff;
    }
    .badge-orange{
        background:#F59E0B;
        color:#fff;
    }
    .badge-primary {
        background-color: #007bff;
        color: white;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    .badge-info {
        background-color: #17a2b8;
        color: white;
    }
    .btn-group .btn {
        font-size: 12px;
        padding: 5px 12px;
        border-radius: 3px;
    }
    .btn-group .btn i {
        font-size: 11px;
    }
    .btn-block {
        display: block;
        width: 100%;
    }
    .modal {
        z-index: 99999 !important;
    }
    .modal-header.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    .modal-header .close {
        color: white;
        opacity: 1;
    }
    .modal-header .close:hover {
        color: white;
        opacity: 0.8;
    }
    label {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
    }
    .form-control-static {
        font-size: 14px;
        padding: 6px 0;
        margin-bottom: 0;
    }
    .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    .alert-warning {
        background-color: #fff8e1;
        border-left: 5px solid #F59E0B;
        color: #856404;
    }
    .alert-warning strong {
        color: #6c4b00;
    }
    .swal2-container {
        z-index: 999999 !important;
    }
</style>

@endsection

@section('script')
<script>
$(document).ready(function () {
    if ($.fn.dataTable.isDataTable('#table')) {
        $('#table').DataTable().destroy();
    }

    $('#table').DataTable({
        pageLength: 10,
        scrollX: true,
        autoWidth: false,
        ordering: false,
        language: {
            search: "Search:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                previous: "Previous",
                next: "Next"
            },
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data purchase order"
        },
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>tip'
    });

    let currentMonth = new Date().toISOString().slice(0,7);
    $('#filterMonth').val(currentMonth);

    function filterTable() {
        let status = $('#filterStatus').val();
        let month = $('#filterMonth').val();
        let search = $('#searchPO').val().toLowerCase();

        $('#table tbody tr').each(function () {
            let row = $(this);
            let rowStatus = row.data('status');
            let rowDate = row.data('date');
            let rowSearch = (row.data('search') || '').toLowerCase();

            let show = true;
            if (status && rowStatus !== status) show = false;
            if (month && rowDate !== month) show = false;
            if (search && !rowSearch.includes(search)) show = false;

            if (show) row.show();
            else row.hide();
        });
    }

    filterTable();

    $('#filterStatus, #filterMonth').on('change', filterTable);
    $('#searchPO').on('keyup', filterTable);

    $('#resetFilter').on('click', function () {
        $('#filterStatus').val('');
        $('#filterMonth').val(currentMonth);
        $('#searchPO').val('');
        filterTable();
    });
});

function hapusPo(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: 'Apakah Anda yakin ingin menghapus Purchase Order ini?<br><strong>Data yang dihapus tidak dapat dikembalikan!</strong>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash mr-1"></i> Ya, Hapus!',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function ubahStatusDikirim(id) {
    Swal.fire({
        title: 'Ubah Status?',
        text: 'Status Purchase Order akan diubah menjadi Dikirim.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-kirim-' + id).submit();
        }
    });
}

function konfirmasiLunas(id){
    Swal.fire({
        title: 'Konfirmasi Pelunasan',
        html:
        'Pastikan pembayaran customer sudah diterima.<br>'+
        '<strong>Status pembayaran akan berubah menjadi Lunas.</strong>',
        icon:'warning',

        showCancelButton:true,
        confirmButtonColor:'#28a745',
        cancelButtonColor:'#6c757d',

        confirmButtonText:
        '<i class="fas fa-check"></i> Ya, Lunas',
        cancelButtonText:
        '<i class="fas fa-times"></i> Batal',

        allowOutsideClick:false,
        allowEscapeKey:false

    }).then((result)=>{
        if(result.isConfirmed){
            document
            .getElementById('form-lunas-'+id)
            .submit();

        }
    });
}

function konfirmasiLunasTable(id){
    Swal.fire({
        title: 'Konfirmasi Pelunasan',
        html:
        'Pastikan pembayaran customer sudah diterima.<br>'+
        '<strong>Status pembayaran akan berubah menjadi Lunas.</strong>',
        icon:'warning',

        showCancelButton:true,
        confirmButtonColor:'#28a745',
        cancelButtonColor:'#6c757d',

        confirmButtonText:
        '<i class="fas fa-check"></i> Ya, Lunas',
        cancelButtonText:
        'Batal'

    }).then((result)=>{
        if(result.isConfirmed){
            document
            .getElementById('form-lunas-table-'+id)
            .submit();

        }
    });
}

function konfirmasiSelesai(id){
    Swal.fire({
        title: 'Selesaikan Purchase Order?',
        html:
        'Status Purchase Order akan berubah menjadi <b>Selesai</b>.<br>'+
        'Invoice akan dapat dicetak setelah proses ini.',
        icon:'question',

        showCancelButton:true,
        confirmButtonColor:'#28a745',
        cancelButtonColor:'#6c757d',
        confirmButtonText:
        '<i class="fas fa-check"></i> Ya, Selesaikan',

        cancelButtonText:'Batal'

    }).then((result)=>{
        if(result.isConfirmed){
            document
            .getElementById('form-selesai-'+id)
            .submit();

        }
    });
}

</script>
@endsection