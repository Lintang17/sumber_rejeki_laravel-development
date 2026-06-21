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
                                    <i class="fas fa-shopping-cart mr-2 text-primary"></i> Purchase Order
                                </h4>
                                <small class="text-muted">Manajemen data purchase order</small>
                            </div>
                            <div>
                                <span class="badge badge-primary badge-lg px-3 py-2">
                                    <i class="fas fa-box mr-1"></i> {{ $po->count() }} Data
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="row mb-4">
                            <div class="col-md-3 mb-2">
                                <div class="form-group">
                                    <label class="font-weight-bold text-secondary small">Status</label>
                                    <select id="filterStatus" class="form-control form-control-sm">
                                        <option value="">-- Semua Status --</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Diproses">Diproses</option>
                                        <option value="Selesai">Selesai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 mb-2">
                                <div class="form-group">
                                    <label class="font-weight-bold text-secondary small">Bulan</label>
                                    <input type="month" id="filterMonth" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-group">
                                    <label class="font-weight-bold text-secondary small">Pencarian</label>
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" id="searchPO" class="form-control" 
                                               placeholder="Cari kode PO atau customer...">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="form-group">
                                    <label class="font-weight-bold text-secondary small">&nbsp;</label>
                                    <button id="resetFilter" class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-undo mr-1"></i> Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="table">
                                <thead style="background-color: #e9ecef;">
                                    <tr>
                                        <th class="text-center" width="40">No</th>
                                        <th>Kode PO</th>
                                        <th>Customer</th>
                                        <th class="text-center" width="70">Foto</th>
                                        <th>Produk</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center" width="50">Jml</th>
                                        <th class="text-center" width="80">Qty</th>
                                        <th>Estimasi</th>
                                        <th>Keterangan</th>
                                        <th class="text-right">HPP Admin</th>
                                        <th class="text-right">HPP Gudang</th>
                                        <th class="text-right text-primary font-weight-bold">HPP Final</th>
                                        <th class="text-right">Harga Jual</th>
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
                                        
                                        <td class="text-center">
                                            @if($item->foto)
                                                <img src="{{ asset('storage/' . $item->foto) }}" 
                                                     width="45" height="45" 
                                                     class="rounded-circle border"
                                                     style="object-fit:cover; cursor:pointer"
                                                     data-toggle="modal"
                                                     data-target="#modalFoto{{ $item->id }}">
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
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
                                                <span class="badge badge-secondary mb-1" style="font-size: 12px; padding: 4px 10px;">{{ $detail->qty }}</span><br>
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
                                                <div style="font-size: 13px;">Rp {{ number_format($detail->hpp_estimasi_admin ?? 0, 0, ',', '.') }}</div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-right">
                                            @foreach($item->detail as $detail)
                                                <div style="font-size: 13px;">Rp {{ number_format($detail->hpp_estimasi_gudang ?? 0, 0, ',', '.') }}</div>
                                            @endforeach
                                        </td>

                                        {{-- HPP FINAL --}}
                                        <td class="text-right font-weight-bold text-primary" style="font-size: 14px;">
                                            @foreach($item->detail as $detail)
                                                <div>Rp {{ number_format($detail->hpp_final ?? 0, 0, ',', '.') }}</div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-right font-weight-bold text-success" style="font-size: 14px;">
                                            @foreach($item->detail as $detail)
                                                <div>Rp {{ number_format($detail->harga_jual ?? 0, 0, ',', '.') }}</div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-center">
                                            @php
                                                $statusBadge = [
                                                    'Pending'   => 'badge-warning',
                                                    'Disetujui' => 'badge-primary',
                                                    'Diproses'  => 'badge-info',
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
                                                   class="btn btn-warning btn-block" style="font-size: 12px; padding: 5px 8px;">
                                                    <i class="fas fa-edit mr-1"></i> Review
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

@foreach($po as $item)
<div class="modal fade" id="modalCustomer{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <h5 class="modal-title">
                    <i class="fas fa-user mr-2 text-primary"></i> Detail Customer
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th width="35%" style="font-size: 13px;">Kode PO</th>
                        <td style="font-size: 14px; font-weight: bold;">{{ $item->kode_po }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 13px;">Nama Customer</th>
                        <td style="font-size: 14px; font-weight: bold;">{{ $item->customer }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 13px;">No. HP</th>
                        <td style="font-size: 14px;">{{ $item->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 13px;">Alamat</th>
                        <td style="font-size: 14px;">{{ $item->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 13px;">Tanggal Order</th>
                        <td style="font-size: 14px;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th style="font-size: 13px;">Status</th>
                        <td>
                            @php
                                $statusBadge = [
                                    'Pending'   => 'badge-warning',
                                    'Disetujui' => 'badge-primary',
                                    'Diproses'  => 'badge-info',
                                    'Selesai'   => 'badge-success',
                                ];
                            @endphp
                            <span class="badge {{ $statusBadge[$item->status] ?? 'badge-secondary' }}" 
                                  style="font-size: 13px; padding: 5px 15px;">
                                {{ $item->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 13px; padding: 6px 18px;">
                    <i class="fas fa-times mr-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Foto --}}
<div class="modal fade" id="modalFoto{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <h5 class="modal-title">
                    <i class="fas fa-image mr-2 text-info"></i> Foto Customer
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                @if($item->foto)
                    <img src="{{ asset('storage/' . $item->foto) }}" 
                         class="img-fluid rounded"
                         style="max-height: 400px;">
                @else
                    <p class="text-muted" style="font-size: 14px;">Tidak ada foto</p>
                @endif
            </div>
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-size: 13px; padding: 6px 18px;">
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
    .modal-header .close {
        color: #000;
        opacity: 0.6;
        font-size: 28px;
    }
    .modal-header .close:hover {
        color: #000;
        opacity: 1;
    }
    .font-weight-bold {
        font-weight: 700;
    }
    .badge-lg {
        padding: 8px 16px;
        font-size: 14px;
    }
    .form-control {
        font-size: 14px;
        height: 38px;
        border-radius: 4px;
        border-color: #ced4da;
    }
    .form-control:focus {
        border-color: #3b7ddd;
        box-shadow: 0 0 0 0.2rem rgba(59, 125, 221, 0.25);
    }
    .form-control-sm {
        height: 35px;
        font-size: 13px;
        border-radius: 4px;
    }
    .form-group label {
        font-size: 13px;
        font-weight: 600;
        color: #495057;
    }
    .input-group-text {
        font-size: 14px;
        border-radius: 4px 0 0 4px;
        background-color: #f8f9fa;
        border-color: #ced4da;
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
            search: "",
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
        dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>tip',
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
        filterTable();
    });
});
</script>
@endsection