@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-0 font-weight-bold">Purchase Order (Gudang)</h4>
                            <small class="text-muted">Kelola semua data purchase order gudang</small>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

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
                        <div class="col-md-2 d-flex align-items-end">
                            <button id="resetFilter" class="btn btn-primary btn-block"
                                style="height:38px; white-space:nowrap;">
                                Reset
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" width="40">#</th>
                                    <th width="150">Kode PO</th>
                                    <th width="150">Customer</th>
                                    <th width="150">Nama Produk</th>
                                    <th width="90" class="text-center">Foto</th>
                                    <th>Deskripsi</th>
                                    <th class="text-center" width="50">Jml</th>
                                    <th class="text-center" width="100">Qty</th>
                                    <th width="130">Estimasi</th>
                                    <th width="100">Keterangan</th>
                                    <th class="text-right" width="120">HPP Estimasi Admin</th>
                                    <th class="text-right" width="140">HPP Estimasi Gudang</th>
                                    @if(auth()->user()->role != 'admin')
                                        <th class="text-right" width="120">HPP Final</th>
                                    @endif
                                    <th class="text-right" width="120">Harga Jual</th>
                                    <th class="text-center" width="110">Status</th>
                                    <th class="text-center" width="230">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($po as $index => $item)
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
                                    <td class="text-center">
                                        @foreach($item->detail as $detail)
                                            @if($detail->foto)
                                               <img src="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                                    class="img-thumbnail foto-po"
                                                    data-toggle="modal"
                                                    data-target="#modalFoto"
                                                    data-foto="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                                    data-produk="{{ $detail->produk }}"
                                                    data-qty="{{ $detail->qty }}"
                                                    style="
                                                        width:60px;
                                                        height:60px;
                                                        object-fit:cover;
                                                        border-radius:10px;
                                                        cursor:pointer;
                                                        margin-bottom:5px;">
                                            @else
                                                <div class="text-muted mb-1">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
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
                                    <td class="text-center">
                                        @if($item->estimasi_awal)
                                            <div style="font-size:15px; font-weight:700; color:#28a745; line-height:1.5;">
                                                <i class="fas fa-play mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->estimasi_awal)->format('d/m/Y') }}
                                            </div>
                                            <div style="font-size:15px; font-weight:700; color:#dc3545; line-height:1.5;">
                                                <i class="fas fa-stop mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->estimasi_akhir)->format('d/m/Y') }}
                                            </div>
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
                                            <div class="mb-1">
                                                Rp {{ number_format($detail->hpp_estimasi_admin ?? 0, 0, ',', '.') }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1">
                                                @if($detail->hpp_estimasi_gudang)
                                                    <span class="text-primary font-weight-bold">
                                                        Rp {{ number_format($detail->hpp_estimasi_gudang, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    @if(auth()->user()->role != 'admin')
                                    <td class="text-right">
                                        @foreach($item->detail as $detail)
                                            <div class="mb-1"><strong class="text-success">Rp {{ number_format($detail->hpp_final ?? 0, 0, ',', '.') }}</strong></div>
                                        @endforeach
                                    </td>
                                    @endif
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
                                                <i class="fas fa-hand-holding mr-1"></i> Diambil
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
                                                <a href="{{ url('gudang/po/'.$item->id.'/detail') }}"
                                                   class="btn btn-block"
                                                   style="background:#6f42c1; border-color:#6f42c1; color:#fff;">
                                                   <i class="fas fa-edit mr-1" style="color:#fff;"></i> Edit
                                                </a>
                                            @elseif($item->status == 'Disetujui')
                                                <form id="proses-form-{{ $item->id }}" 
                                                      action="{{ url('gudang/po/'.$item->id.'/proses') }}" 
                                                      method="POST">
                                                    @csrf
                                                    <button type="button" 
                                                            onclick="prosesPo({{ $item->id }})"
                                                            class="btn btn-warning btn-block">
                                                        <i class="fas fa-play mr-1"></i> Proses
                                                    </button>
                                                </form>
                                            @elseif($item->status == 'Diproses')
                                                <div class="d-flex flex-column" style="gap:5px">
                                                    {{-- Print Internal Gudang --}}
                                                    <a href="{{ url('gudang/po/'.$item->id.'/print') }}" 
                                                       target="_blank"
                                                       class="btn btn-primary btn-sm">
                                                       <i class="fas fa-print mr-1"></i> Print Internal
                                                    </a>
                                                    {{-- Ubah Status Diambil --}}
                                                    <form action="{{ url('gudang/po/'.$item->id.'/update') }}" 
                                                          method="POST"
                                                          id="ambil-form-{{ $item->id }}">
                                                         @csrf
                                                        <input type="hidden" name="status" value="Diambil">
                                                        <button type="button"
                                                                onclick="ambilPo({{ $item->id }})"
                                                                class="btn btn-success btn-sm btn-block">
                                                            <i class="fas fa-check mr-1"></i> Selesai Produksi
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($item->status == 'Diambil')
                                                <a href="{{ url('gudang/po/'.$item->id.'/print') }}" 
                                                   target="_blank"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-file-pdf mr-1"></i> Surat Pengambilan
                                                </a>
                                            @else
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fas fa-lock mr-1"></i> Terkunci
                                                </button>
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

<!-- Modal Detail-->
@foreach($po as $item)
<div class="modal fade" id="customer{{ $item->id }}" tabindex="-1">
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
                                    <span class="badge badge-secondary badge-lg">
                                        <i class="fas fa-box mr-1"></i> Diambil
                                    </span>
                                @elseif($item->status == 'Dikirim')
                                    <span class="badge badge-dark badge-lg">
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

<!-- Modal Detail-->
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-foto">

            <div class="modal-header">
                <h5 class="modal-title mb-0">
                    <span id="judulProduk"></span>
                    <span class="ml-2 text-dark">
                        <strong>(Qty : <span id="qtyProdukHeader"></span>)</strong>
                    </span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="previewFoto" class="foto-preview" src="">
            </div>
        </div>
    </div>
</div>

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
    .badge-purple{
        background:#6f42c1;
        color:#fff;
    }
    .badge-orange{
        background:#fd7e14;
        color:#fff;
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
    .modal{
        z-index: 99999 !important;
    }
    .modal-backdrop{
        z-index:1060 !important;
    }
    .modal-dialog{
        margin-top:40px;
    }
    .modal-content{
        border:none;
        border-radius:16px;
        overflow:hidden;
        box-shadow:0 15px 40px rgba(0,0,0,.18); 
    }
    .modal-header{
        border-bottom:none;
    }
    .modal-footer{
        border-top:none;
    }
    .modal-body{
        max-height:70vh;
        overflow-y:auto;
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
    .modal-xl{
        max-width:650px;
    }
    .modal-foto{
        border-radius:8px;
        overflow:hidden;
        border:none;
    }
    .modal-foto .modal-header{
        background:#fff;
        padding:16px 22px;
        border-bottom:1px solid #e5e5e5;
    }
    .modal-foto .modal-title{
        font-size:22px;
        font-weight:700;
        color:#2d3748;
    }   
    .modal-foto .close{
        font-size:30px;
        opacity:.6;
        outline:none;
    }
    .modal-foto .close:hover{
        opacity:1;
    }
    .modal-foto .modal-body{
        background:#f7f7f7;
        padding:10px;
        text-align:center;
    }
    .foto-preview{
        width:100%;
        max-height:85vh;
        object-fit:contain;
        border-radius:6px;
        background:#fff;
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
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd6 0%, #6a4292 100%);
    }
    #resetFilter {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    #resetFilter:hover {
        background: linear-gradient(135deg, #5a6fd6 0%, #6a4292 100%);
        color: white;
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
        responsive: true,
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

// Ubah Status Menjadi 'Diproses'
function prosesPo(id) {
    Swal.fire({
        title: 'Konfirmasi Proses PO',
        text: 'Apakah Anda yakin ingin memproses PO ini? Status akan berubah menjadi Diproses.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#5b0ff3',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-play mr-1"></i> Ya, Proses',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('proses-form-' + id).submit();
        }
    });
}

// Ubah Status Menjadi 'Diambil'
function ambilPo(id) {
    Swal.fire({
        title: 'Konfirmasi Selesai Produksi',
        text: 'Apakah produksi PO ini sudah selesai dan siap diambil?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Selesai',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal'
    }).then((result)=>{

        if(result.isConfirmed){
            document.getElementById('ambil-form-'+id).submit();
        }
    });
}

$(document).on('click', '.foto-po', function () {
    $('#previewFoto').attr('src', $(this).data('foto'));
    $('#judulProduk').text($(this).data('produk'));
    $('#qtyProdukHeader').text($(this).data('qty'));
});
</script>
@endsection