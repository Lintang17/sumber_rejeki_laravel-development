@extends('layouts.admin')

@section('content')
<div class="content-wrapper">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-weight-bold mb-1">
                        Purchase Order
                    </h3>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row g-3 mb-3 align-items-end filter-box">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select id="filterStatus" class="form-control filter-input">
                        <option value="">Semua Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bulan</label>
                    <input type="month" id="filterMonth" class="form-control filter-input">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cari PO</label>
                    <input type="text" id="searchPO" class="form-control filter-input"
                           placeholder="Kode PO / Customer">
                </div>

                <div class="col-md-2">
                    <button id="resetFilter" class="btn btn-primary w-100">
                        Reset
                    </button>
                </div>

            </div>

            <div class="table-responsive custom-scroll">
                <table class="table custom-table align-middle" id="table">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode PO</th>
                            <th>Customer</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th>Qty</th>
                            <th>Estimasi</th>
                            <th>Keterangan</th>
                            <th>HPP Estimasi</th>
                            <th>Harga Jual</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    @forelse($po as $index => $item)

                    <tr 
                        data-status="{{ $item->status }}"
                        data-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m') }}"
                        data-search="{{ $item->kode_po }} {{ $item->customer }}">

                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>
                                {{ $item->kode_po }}
                            </strong>

                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </small>
                        </td>

                        <td>
                            {{ $item->customer }}
                        </td>

                        <td style="min-width:220px;">
                            @foreach($item->detail as $detail)
                                <div class="item-box">
                                    {{ $detail->produk }}
                                </div>
                            @endforeach
                        </td>

                        <td style="min-width:250px;">
                            @foreach($item->detail as $detail)
                                <div class="item-box">
                                    {{ $detail->deskripsi ?? '-' }}
                                </div>
                            @endforeach
                        </td>

                        <td>
                            <span class="qty-badge">
                                {{ $item->detail->count() }} 
                            </span>
                        </td>

                        <td>
                            @foreach($item->detail as $detail)
                                <div class="item-box text-center">
                                    {{ $detail->qty }}
                                </div>
                            @endforeach
                        </td>

                        <td style="min-width:180px;">
                            <div class="estimasi-box">
                                <small>Awal</small>
                                <br>

                                {{ $item->estimasi_awal
                                    ? \Carbon\Carbon::parse($item->estimasi_awal)->format('d M Y')
                                    : '-' }}

                                <hr>

                                <small>Akhir</small>
                                <br>

                                {{ $item->estimasi_akhir
                                    ? \Carbon\Carbon::parse($item->estimasi_akhir)->format('d M Y')
                                    : '-' }}
                            </div>
                        </td>

                        <td>
                            <div class="estimasi-box">
                                {{ $item->keterangan ?? '-' }}
                            </div>
                        </td>

                        <td>
                            @foreach($item->detail as $detail)
                                <div class="hpp-box">
                                    Rp {{ number_format($detail->hpp_estimasi,0,',','.') }}
                                </div>
                            @endforeach
                        </td>

                        <td>
                            @foreach($item->detail as $detail)
                                <div class="harga-box">
                                    Rp {{ number_format($detail->harga_jual,0,',','.') }}
                                </div>
                            @endforeach
                        </td>

                        <td>
                            @if($item->status == 'Pending')
                                <span class="status-badge warning">
                                    Pending
                                </span>

                            @elseif($item->status == 'Disetujui')
                                <span class="status-badge success">
                                    Disetujui
                                </span>

                            @elseif($item->status == 'Diproses')
                                <span class="status-badge info">
                                    Diproses
                                </span>

                            @elseif($item->status == 'Selesai')
                                <span class="status-badge done">
                                    Selesai
                                </span>

                            @else
                                <span class="status-badge danger">
                                    Dibatalkan
                                </span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ url('owner/po/'.$item->id.'/review') }}"
                               class="btn btn-sm btn-warning">
                                    Edit
                            </a>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="13" class="text-center">
                            Belum ada data purchase order
                        </td>
                    </tr>

                    @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>

.custom-scroll{
    overflow-x:auto;
}

.filter-wrapper{
    background:#f8fafc;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:18px;
}

.filter-input{
    height:45px;
    border-radius:10px;
    border:1px solid #d1d5db;
}

.filter-input:focus{
    box-shadow:none;
}

.btn-reset{
    height:45px;
    border-radius:10px;
    background:#e5e7eb;
    color:#111827;
    border:none;
}

.custom-table{
    min-width:1300px;
    border-collapse:separate;
    border-spacing:0 10px;
}

.custom-table thead th{
    border:none !important;
    background:#f8fafc;
    padding:14px;
    font-size:13px;
    color:#6b7280;
}

.custom-table tbody tr{
    background:#fff;
    box-shadow:0 2px 8px rgba(0,0,0,.04);
}

.custom-table td{
    padding:14px;
    vertical-align:middle;
}

.item-box{
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:8px 10px;
    margin-bottom:6px;
}

.qty-badge{
    background:#f3f4f6;
    color:#111827;
    padding:6px 12px;
    border-radius:8px;
    font-weight:600;
}

.estimasi-box{
    background:#f9fafb;
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:10px;
}

.hpp-box{
    background:#ecfdf5;
    color:#059669;
    padding:8px 10px;
    border-radius:8px;
    margin-bottom:6px;
}

.harga-box{
    background:#eff6ff;
    color:#2563eb;
    padding:8px 10px;
    border-radius:8px;
    margin-bottom:6px;
}

.status-badge{
    padding:7px 12px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
}

.warning{
    background:#fff7ed;
    color:#ea580c;
}

.info{
    background:#eff6ff;
    color:#2563eb;
}

.success{
    background:#ecfdf5;
    color:#059669;
}

.done{
    background:#ecfccb;
    color:#4d7c0f;
}

.danger{
    background:#fef2f2;
    color:#dc2626;
}

.action-btn{
    width:38px;
    height:38px;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
}

</style>
@endsection

@section('script')

<script>
$(document).ready(function () {

    if ($.fn.dataTable.isDataTable('#table')) {
        $('#table').DataTable().destroy();
    }

    let table = $('#table').DataTable({
        pageLength: 5,
        responsive: true,
        language: {
            search: "",
            lengthMenu: "_MENU_",
            info: "_START_ - _END_ / _TOTAL_",
            paginate: { previous: "‹", next: "›" },
            zeroRecords: "Data tidak ditemukan",
            emptyTable: "Belum ada data purchase order"
        }
    });

    let currentMonth = new Date().toISOString().slice(0,7);
    $('#filterMonth').val(currentMonth);

    function filterTable() {

    $.fn.dataTable.ext.search = [];

    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {

        let status = $('#filterStatus').val();
        let month = $('#filterMonth').val();
        let search = $('#searchPO').val().toLowerCase();

        let rowNode = table.row(dataIndex).node();

        let rowStatus = $(rowNode).data('status');
        let rowDate = $(rowNode).data('date');
        let rowSearch = ($(rowNode).data('search') || '').toLowerCase();

        if (status && rowStatus !== status) return false;
        if (month && rowDate !== month) return false;
        if (search && !rowSearch.includes(search)) return false;

        return true;
    });

    table.draw();
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
</script>

@endsection
