@extends('layouts.admin')

@section('content')
<div class="content-wrapper">

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="font-weight-bold mb-0">
                    Purchase Order (Gudang)
                </h3>
            </div>

            <div class="row g-3 mb-3 align-items-end filter-box">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select id="filterStatus" class="form-control filter-input">
                        <option value="">Semua Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Disetujui">Disetujui</option>
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
                            data-search="{{ $item->kode_po }} {{ $item->customer }}"
                        >

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
                                    <div class="mb-1">{{ $detail->produk }}</div>
                                @endforeach
                            </td>

                            <td>
                                @foreach($item->detail as $detail)
                                    <div>{{ $detail->deskripsi ?? '-' }}</div>
                                @endforeach
                            </td>

                            <td>
                                <span class="qty-badge">
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
                                    <div>Rp {{ number_format($detail->hpp_estimasi,0,',','.') }}</div>
                                @endforeach
                            </td>

                            <td>
                                @foreach($item->detail as $detail)
                                    <div>Rp {{ number_format($detail->harga_jual,0,',','.') }}</div>
                                @endforeach
                            </td>

                            <td>
                                @if($item->status == 'Pending')
                                    <span class="status-badge warning">Pending</span>

                                @elseif($item->status == 'Disetujui')
                                    <span class="status-badge info">Disetujui</span>

                                @elseif($item->status == 'Diproses')
                                    <span class="status-badge info">Diproses</span>

                                @elseif($item->status == 'Selesai')
                                    <span class="status-badge success">Selesai</span>

                                @else
                                    <span class="status-badge danger">Dibatalkan</span>
                                @endif
                            </td>

                            <td>
                                @if($item->status != 'Selesai')
                                    <a href="{{ url('gudang/po/'.$item->id.'/detail') }}"
                                        class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled>
                                        Locked
                                    </button>
                                @endif
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
.filter-box{
    background:#f8fafc;
    padding:15px;
    border-radius:16px;
    border:1px solid #e5e7eb;
}

.filter-input{
    border-radius:12px;
    height:45px;
}

.custom-scroll{
    overflow-x:auto;
}

.custom-table{
    min-width:1500px;
    border-collapse:separate;
    border-spacing:0 10px;
}

.custom-table tbody tr{
    background:#fff;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
}

.custom-table tbody td{
    padding:14px;
    vertical-align:middle;
}

.status-badge{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
}

.status-badge.warning{background:#fff7ed;color:#ea580c;}
.status-badge.info{background:#eff6ff;color:#2563eb;}
.status-badge.success{background:#ecfdf5;color:#059669;}
.status-badge.danger{background:#fee2e2;color:#dc2626;}

.qty-badge{
    background:#2563eb;
    color:#fff !important;
    padding:6px 12px;
    border-radius:20px;
    font-weight:600;
    display:inline-block;
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
