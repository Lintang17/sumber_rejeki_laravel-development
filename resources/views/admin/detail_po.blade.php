@extends('layouts.admin')

@section('content')
<div class="content-wrapper">

<div class="card shadow-sm border-0">

    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0 fw-bold">Purchase Order</h3>
                <small class="text-muted">Detail Purchase Order</small>
            </div>

            <a href="{{ url('admin/po/tambah') }}" class="btn btn-primary">
                + Tambah PO
            </a>
        </div>

        <div class="row g-3 mb-3 align-items-end filter-box">
            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Status</label>
                <select id="filterStatus" class="form-control filter-input status-filter">
                    <option value="">Semua Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold text-dark">Bulan</label>
                <input type="month" id="filterMonth" class="form-control filter-input month-filter">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold text-dark">Cari PO</label>
                <input type="text" id="searchPO" class="form-control filter-input search-filter" placeholder="Kode PO / Customer">
            </div>

            <div class="col-md-2">
                <button id="resetFilter" class="btn btn-primary w-100">
                    Reset
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle" id="table">
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

                @foreach($po as $index => $item)

                <tr data-status="{{ $item->status }}"
                    data-date="{{ \Carbon\Carbon::parse($item->tanggal)->format('Y-m') }}"
                    data-search="{{ $item->kode_po }} {{ $item->customer }}">

                    <td>{{ $index + 1 }}</td>

                    <td>
                        <strong>{{ $item->kode_po }}</strong><br>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                        </small>
                    </td>

                    <td>{{ $item->customer }}</td>

                    <td>
                        @foreach($item->detail as $detail)
                            <div class="mb-1">
                                <span class="badge bg-light text-dark border">
                                    {{ $detail->produk }}
                                </span>
                            </div>
                        @endforeach
                    </td>

                    <td>
                        @foreach($item->detail as $detail)
                            <div>{{ $detail->deskripsi ?? '-' }}</div>
                        @endforeach
                    </td>

                    <td>
                        <span class="badge bg-primary text-white">
                            {{ $item->detail->count() }} Produk
                        </span>
                    </td>

                    <td>
                        @foreach($item->detail as $detail)
                            <div>{{ $detail->qty }}</div>
                        @endforeach
                    </td>

                    <td>
                        <small>
                            {{ $item->estimasi_awal ? \Carbon\Carbon::parse($item->estimasi_awal)->format('d M Y') : '-' }}
                        </small><br>
                        <small>
                            {{ $item->estimasi_akhir ? \Carbon\Carbon::parse($item->estimasi_akhir)->format('d M Y') : '-' }}
                        </small>
                    </td>

                    <td>{{ $item->keterangan ?? '-' }}</td>

                    <td>
                        @foreach($item->detail as $detail)
                            <div>Rp {{ number_format($detail->hpp_estimasi, 0, ',', '.') }}</div>
                        @endforeach
                    </td>

                    <td>
                        @foreach($item->detail as $detail)
                            <div>Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</div>
                        @endforeach
                    </td>

                    <td>
                        @if($item->status == 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif($item->status == 'Diproses')
                            <span class="badge bg-info">Diproses</span>
                        @elseif($item->status == 'Selesai')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="badge bg-danger text-white">Dibatalkan</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex gap-1">

                            @if($item->status == 'Pending')
                                <a href="{{ url('admin/po/edit/'.$item->id) }}" class="btn btn-sm btn-warning">
                                    Edit
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled>Lock</button>
                            @endif

                            @if($item->status == 'Selesai')
                                <a href="{{ url('admin/po/print/'.$item->id) }}" class="btn btn-sm btn-primary">
                                    Print
                                </a>
                            @endif

                            <form action="{{ url('admin/po/hapus/'.$item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus data?')">
                                    Hapus
                                </button>
                            </form>
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

<style>

.filter-box{
    background:#f8fafc;
    padding:15px;
    border-radius:16px;
    border:1px solid #e5e7eb;
}

.filter-input{
    border-radius:12px;
    height:45px;
    border:1px solid #d1d5db;
}

.filter-input:focus{
    border-color:#6366f1;
    box-shadow:0 0 0 3px rgba(99,102,241,0.2);
}

.status-filter{ background:#eef2ff; }
.month-filter{ background:#ecfdf5; }
.search-filter{ background:#fffbeb; }

.badge.bg-primary{
    color:#fff !important;
    font-weight:600;
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

        $('#table tbody tr').each(function () {

            let row = $(this);

            let status = $('#filterStatus').val();
            let month = $('#filterMonth').val();
            let search = $('#searchPO').val().toLowerCase();

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

        table.draw(false);
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