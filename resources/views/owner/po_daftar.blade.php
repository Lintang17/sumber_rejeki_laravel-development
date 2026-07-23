@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="card card-outline shadow-sm" style="border-radius: 8px; overflow: hidden; border-top: 3px solid #3b7ddd;">
                    
                    <div class="card-header bg-white py-3" style="border-bottom: 1px solid #e9ecef;">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0 font-weight-bold text-dark">
                                    Purchase Order
                                </h4>
                            </div>
                            <div>
                                <span class="badge badge-primary badge-lg px-3 py-2">
                                    <i class="fas fa-box mr-1"></i> {{ $po->count() }} Data
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Informasi --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex align-items-start p-3"
                                     style="background:#FFF8E6; border:1px solid #F4D35E; border-left:5px solid #E9B949; border-radius:8px;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 font-weight-bold" style="color:#8A5A00;">
                                            Informasi Review PO
                                        </h6>
                                        <p class="mb-0 text-dark" style="line-height:1.7;">
                                            Owner bertugas melakukan
                                            <strong>review PO</strong> dengan cara
                                            mengisi <strong>HPP Final</strong> dan
                                            <strong>Harga Jual</strong>, kemudian melakukan
                                            <strong>Approval PO</strong> agar proses produksi dapat
                                            dilanjutkan oleh pihak Gudang.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4 align-items-end">
                            <div class="col-md-3">
                                <label class="font-weight-bold text-secondary mb-1">Status</label>
                                <select id="filterStatus" class="form-control" style="height:38px !important; padding:0 12px !important;">                                
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
                                <input type="month" id="filterMonth" class="form-control" style="height:38px !important; padding:0 12px !important;">
                            </div>
                            <div class="col-md-4">
                                <label class="font-weight-bold text-secondary mb-1">Pencarian</label>
                                <div class="input-group" style="height:38px;">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white" style="height:38px;">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                          id="searchPO"
                                          class="form-control"
                                          style="height:38px !important;"
                                          placeholder="Cari kode PO atau customer...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="font-weight-bold text-secondary mb-1">&nbsp;</label>
                                <button id="resetFilter"
                                        class="btn btn-primary btn-block" style="height:38px;">
                                    <i class="fas fa-undo mr-2"></i>
                                    Reset
                                </button>
                            </div>
                        </div>    

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="table">
                                <thead style="background-color: #e9ecef;">
                                    <tr>
                                        <th class="text-center" width="40">No</th>
                                        <th>Kode PO</th>
                                        <th>Customer</th>
                                        <th>Produk</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center" width="50">Jml</th>
                                        <th class="text-center" width="80">Qty</th>
                                        <th>Estimasi</th>
                                        <th>Keterangan</th>
                                        <th class="text-right">HPP Admin</th>
                                        <th class="text-right">HPP Gudang</th>
                                        <th class="text-right font-weight-bold"
                                            style="background:#FFF3CD;color:#8A5A00;border-bottom:2px solid #E9C46A;">
                                            HPP Final
                                        </th>
                                        <th class="text-right font-weight-bold"
                                            style="background:#FFF3CD;color:#8A5A00;border-bottom:2px solid #E9C46A;">
                                            Harga Jual
                                        </th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($po as $index => $item)
                                    <tr data-status="{{ $item->status }}"
                                        data-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m') }}"
                                        data-search="{{ $item->kode_po }} {{ $item->customer }}">
                                        
                                        <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                        
                                        <td>
                                            <span class="font-weight-bold" style="font-size: 14px;">{{ $item->kode_po }}</span>
                                            <br>
                                            <small class="text-muted" style="font-size: 12px;">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                                            </small>
                                        </td>
                                        
                                        <td>
                                            <span class="font-weight-bold" style="font-size: 14px;">{{ $item->customer }}</span>
                                            <br>
                                            <a href="#" class="text-info" data-toggle="modal" 
                                               data-target="#modalCustomer{{ $item->id }}" style="font-size: 12px;">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                        </td>
                                        
                                        <td>
                                            @foreach($item->detail as $detail)
                                                <span class="badge badge-primary mb-1" style="font-size: 12px; padding: 4px 10px;">{{ $detail->produk }}</span><br>
                                            @endforeach
                                        </td>
                                        
                                        <td>
                                            @foreach($item->detail as $detail)
                                                <div style="font-size: 13px;">{{ $detail->deskripsi ?? '-' }}</div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-center">
                                            <span class="badge badge-info" style="font-size: 12px; padding: 4px 10px;">{{ $item->detail->count() }}</span>
                                        </td>
                                        
                                        <td>
                                            @foreach($item->detail as $detail)
                                                <span class="badge mb-1"
                                                      style="background:#4F46E5;color:#fff;font-size:12px;padding:6px 12px;border-radius:20px;font-weight:600;">
                                                    {{ $detail->qty }}
                                                </span>
                                            @endforeach
                                        </td>
                                        
                                        <td>
                                            <div style="font-size: 12px;">
                                                <span class="text-success">
                                                    <i class="fas fa-play mr-1"></i>
                                                    {{ $item->estimasi_awal ? \Carbon\Carbon::parse($item->estimasi_awal)->format('d/m/Y') : '-' }}
                                                </span>
                                                <br>
                                                <span class="text-danger">
                                                    <i class="fas fa-stop mr-1"></i>
                                                    {{ $item->estimasi_akhir ? \Carbon\Carbon::parse($item->estimasi_akhir)->format('d/m/Y') : '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            @if($item->keterangan)
                                                <span class="badge badge-warning" style="font-size: 12px; padding: 4px 10px;">{{ $item->keterangan }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        
                                        <td class="text-right">
                                            @foreach($item->detail as $detail)
                                                <div style="font-size: 14px;">Rp {{ number_format($detail->hpp_estimasi_admin ?? 0, 0, ',', '.') }}</div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-right">
                                            @foreach($item->detail as $detail)
                                                <div style="font-size: 14px;">Rp {{ number_format($detail->hpp_estimasi_gudang ?? 0, 0, ',', '.') }}</div>
                                            @endforeach
                                        </td>

                                        {{-- HPP FINAL --}}
                                        <td class="text-right"
                                            style="background:#FFFDF5;color:#8A5A00;font-size:14px;font-weight:700;border-left:2px solid #F4D35E;">
                                            @foreach($item->detail as $detail)
                                                <div style="padding:5px 0;">
                                                    Rp {{ number_format($detail->hpp_final ?? 0, 0, ',', '.') }}
                                                </div>
                                            @endforeach
                                        </td>

                                        <td class="text-right"
                                            style="background:#FFFDF5;color:#8A5A00;font-size:14px;font-weight:700;border-right:2px solid #F4D35E;">
                                            @foreach($item->detail as $detail)
                                                <div style="padding:5px 0;">
                                                    Rp {{ number_format($detail->harga_jual ?? 0, 0, ',', '.') }}
                                                </div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-center">
                                            @php
                                                $statusBadge = [
                                                    'Pending'   => 'badge-warning',
                                                    'Disetujui' => 'badge-primary',
                                                    'Diproses'  => 'badge-info',
                                                    'Diambil'   => 'badge-purple',
                                                    'Dikirim'   => 'badge-orange',
                                                    'Selesai'   => 'badge-success',
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusBadge[$item->status] ?? 'badge-secondary' }}" 
                                                  style="font-size: 12px; padding: 5px 12px;">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        
                                        <td>
                                            @if($item->status == 'Selesai')
                                                <button class="btn btn-secondary btn-block" disabled style="font-size: 12px; padding: 5px 8px;">
                                                    <i class="fas fa-lock mr-1"></i> Terkunci
                                                </button>
                                            @else
                                                <a href="{{ url('owner/po/'.$item->id.'/review') }}" 
                                                    class="btn btn-block"
                                                    style="background:#7C3AED;border-color:#7C3AED;color:#fff;font-size:12px;padding:6px 8px;font-weight:600;">
                                                    <i class="fas fa-clipboard-check mr-1"></i>
                                                    Review
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="16" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted d-block mb-3"></i>
                                            <h5 class="text-muted">Belum ada data purchase order</h5>
                                            <small class="text-muted">Silakan tambahkan data PO baru</small>
                                        </td>
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
</div>

<!-- Modal Customer Detail -->
@foreach($po as $item)
<div class="modal fade" id="modalCustomer{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">
                    <i class="fas fa-user mr-2"></i> Detail Customer
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

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Status Pembayaran</label>

                            @if($item->status_pembayaran == 'DP')
                                <span class="badge badge-warning badge-lg">
                                    <i class="fas fa-wallet mr-1"></i> DP
                                </span>
                            @elseif($item->status_pembayaran == 'Lunas')
                                <span class="badge badge-success badge-lg">
                                    <i class="fas fa-check-circle mr-1"></i> Lunas
                                </span>
                            @endif
                        </div>
                    </div>

                    @php
                        $hargaBelumAda = $item->detail->contains(function($d){
                            return empty($d->harga_jual) || $d->harga_jual <= 0;
                        });
                        if ($item->status_pembayaran == 'Lunas') {
                            $sisa = 0;
                        } else {
                            $sisa = $item->sisa_pembayaran ?? 0;
                        }
                    @endphp
                    <div class="mt-3 p-3 rounded" style="background:#f8f9fa;">
                        <label class="font-weight-bold mb-1">
                            Sisa Pembayaran
                        </label>

                        @if($hargaBelumAda)
                            <p class="mb-0 text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                Menunggu Owner mengisi harga jual
                            </p>
                        @else
                            @if($item->status_pembayaran == 'Lunas')
                                <p class="mb-0 text-success font-weight-bold">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Lunas (Rp 0)
                                </p>
                            @else
                                <p class="mb-0">
                                    <strong>
                                        Rp {{ number_format($sisa,0,',','.') }}
                                    </strong>
                                </p>
                            @endif
                        @endif
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Status</label>
                            <p>
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
                                        <i class="fas fa-cogs mr-1"></i> Diproses
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

<style>
    .table th {
        font-size: 13px;
        white-space: nowrap;
        padding: 10px 8px;
        border-color: #dee2e6;
    }
    .table td {
        font-size: 14px;
        padding: 10px 8px;
        vertical-align: middle;
        border-color: #dee2e6;
    }
    .badge {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 4px;
    }
    .btn {
        font-size: 14px;
        padding: 6px 12px;
        border-radius: 4px;
    }
    .btn-block {
        display: block;
        width: 100%;
    }
    .table thead th {
        background-color: #e9ecef;
        color: #495057;
        border-color: #dee2e6;
        font-weight: 600;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table-bordered td, .table-bordered th {
        border: 1px solid #dee2e6;
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
    .font-weight-bold {
        font-weight: 700;
    }
    .badge-lg {
        padding: 8px 16px;
        font-size: 14px;
    }
    .form-control {
        border-radius: 4px;
        border: 1px solid #ced4da;
        height: 38px;
        font-size: 14px;
    }
    .form-control:focus {
        border-color: #3b7ddd;
        box-shadow: 0 0 0 0.2rem rgba(59, 125, 221, 0.25);
    }
    .form-control-sm {
        font-size: 13px;
        border-radius: 4px;
    }
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
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
    .input-group-sm .input-group-text {
        font-size: 13px;
        border-radius: 4px 0 0 4px;
    }
    .shadow-sm {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .card {
        border-radius: 8px !important;
        border-color: #e9ecef !important;
    }
    .card-header {
        border-radius: 8px 8px 0 0 !important;
    }
    .table-responsive {
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .text-primary {
        color: #3b7ddd !important;
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-warning {
        color: #ffc107 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .text-info {
        color: #17a2b8 !important;
    }
    .btn-primary {
        background-color: #3b7ddd;
        border-color: #3b7ddd;
        color: #fff;
    }
    .btn-primary:hover {
        background-color: #2b6cb5;
        border-color: #2b6cb5;
    }
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    .badge-purple{
        background:#6f42c1;
        color:#fff;
    }
    .badge-orange{
        background:#fd7e14;
        color:#fff;
    }
    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    if ($.fn.dataTable.isDataTable('#table')) {
        $('#table').DataTable().destroy();
    }

    let table = $('#table').DataTable({
        pageLength: 10,
        scrollX: true,
        autoWidth: false,
        ordering: false,
        language: {
            search: "Search:",
            lengthMenu: "Tampilkan _MENU_ per halaman",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                previous: "<",
                next: ">"
            },
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data purchase order"
        },
        dom:
            '<"row mb-2"<"col-md-6"l><"col-md-6 text-right"f>>' +
            'rt' +
            '<"row mt-2"<"col-md-6"i><"col-md-6"p>>',
        columnDefs: [
            { orderable: false, targets: '_all' }
        ]
    });

    let currentMonth = new Date().toISOString().slice(0,7);
    $('#filterMonth').val(currentMonth);

    function filterTable() {
        $.fn.dataTable.ext.search = [];
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            let status = $('#filterStatus').val();
            let month = $('#filterMonth').val();
            let search = $('#searchPO').val().toLowerCase();

            let row = table.row(dataIndex).node();
            let rowStatus = $(row).data('status');
            let rowDate = $(row).data('date');
            let rowSearch = ($(row).data('search') || '').toLowerCase();

            if (status && rowStatus !== status) return false;
            if (month && rowDate !== month) return false;
            if (search && !rowSearch.includes(search)) return false;

            return true;
        });
        table.draw();
    }

    filterTable();

    $('#filterStatus, #filterMonth').on('change', filterTable);
    $('#searchPO').on('keyup', function() {
        filterTable();
    });

    $('#resetFilter').on('click', function() {
        $('#filterStatus').val('');
        $('#filterMonth').val(currentMonth);
        $('#searchPO').val('');
        table.search('').draw(); 
        filterTable();
    });
});
</script>
@endsection