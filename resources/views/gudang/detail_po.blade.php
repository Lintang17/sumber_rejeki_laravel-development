@extends('layouts.admin')

@section('content')
<div class="content-wrapper">

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div>
                    <h3 class="font-weight-bold mb-1">
                        Detail Purchase Order
                    </h3>

                    <p class="text-muted mb-0">
                        Update purchase order.
                    </p>
                </div>

                <div>
                    @if($po->status == 'Pending')
                        <span class="status-badge warning">Pending</span>
                    @elseif($po->status == 'Disetujui')
                        <span class="status-badge info">Disetujui</span>
                    @elseif($po->status == 'Diproses')
                        <span class="status-badge info">Diproses</span>
                    @elseif($po->status == 'Diambil')
                        <span class="status-badge success">Diambil</span>
                    @elseif($po->status == 'Dikirim')
                        <span class="status-badge success">Dikirim</span>
                    @elseif($po->status == 'Selesai')
                        <span class="status-badge success">Selesai</span>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <div class="info-card">
                        <small>Kode PO</small>
                        <h6 class="mb-0 fw-bold">
                            {{ $po->kode_po }}
                        </h6>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="info-card">
                        <small>Customer</small>
                        <h6 class="mb-0 fw-bold">
                            {{ $po->customer }}
                        </h6>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="info-card">
                        <small>Tanggal</small>
                        <h6 class="mb-0 fw-bold">
                            {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}
                        </h6>
                    </div>
                </div>
            </div>

            <form action="{{ url('gudang/po/' . $po->id . '/update') }}"
                  method="POST">
                @csrf

                <div class="table-responsive custom-scroll mb-3">
                    <table class="table custom-table align-middle">

                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Deskripsi</th>
                                <th>Qty</th>
                                <th style="width:100px; min-width:100px;">
                                    Jumlah Produk
                                </th>
                                <th>HPP Estimasi</th>
                                <th>Harga Jual</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($po->detail as $index => $detail)

                            <tr>
                                <td>
                                    <div class="produk-box">
                                        <strong>
                                            {{ $detail->produk }}
                                        </strong>
                                    </div>
                                </td>

                                <td style="min-width:220px;">
                                    {{ $detail->deskripsi ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="qty-badge">
                                        {{ $detail->qty }}
                                    </span>
                                </td>

                                @if($index == 0)
                                <td class="text-center align-middle"
                                    rowspan="{{ $po->detail->count() }}"
                                    style="width:100px; min-width:100px;">

                                    <div class="produk-total-box">
                                        {{ $po->detail->count() }}
                                        <small>Produk</small>
                                    </div>

                                </td>
                                @endif

                                <td>
                                    @if($detail->hpp_estimasi_gudang > 0)
                                        <div class="mb-1">
                                            <small class="text-muted d-block">
                                                HPP Gudang
                                            </small>
                                            <span class="text-success fw-semibold">
                                                Rp {{ number_format($detail->hpp_estimasi_gudang,0,',','.') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if($detail->hpp_estimasi_admin > 0)
                                        <div>
                                            <small class="text-muted d-block">
                                                HPP Admin
                                            </small>

                                            <span class="text-primary fw-semibold">
                                                Rp {{ number_format($detail->hpp_estimasi_admin,0,',','.') }}
                                            </span>
                                        </div>
                                    @endif

                                    @if(
                                        ($detail->hpp_estimasi_gudang ?? 0) <= 0 &&
                                        ($detail->hpp_estimasi_admin ?? 0) <= 0
                                    )
                                        <span class="text-muted">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="text-primary fw-semibold">
                                    Rp {{ number_format($detail->harga_jual,0,',','.') }}
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">
                                    Estimasi Akhir
                                </label>

                                <input type="date"
                                       {{ $po->status == 'Selesai' ? 'disabled' : '' }}
                                       name="estimasi_akhir"
                                       class="form-control"
                                       value="{{ $po->estimasi_akhir }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="fw-semibold">
                                    Status PO
                                </label>

                                @if($po->status == 'Pending')
                                    <div class="alert alert-warning py-2 px-3 mb-2">
                                        Menunggu approval owner.
                                        Gudang hanya dapat mengisi estimasi akhir dan keterangan.
                                    </div>

                                @elseif($po->status == 'Disetujui')
                                    <div class="alert alert-info py-2 px-3 mb-2">
                                        Owner telah menyetujui PO.
                                        Gudang dapat memulai proses PO.
                                    </div>

                                @elseif($po->status == 'Diproses')
                                    <div class="alert alert-primary py-2 px-3 mb-2">
                                        PO sedang diproses gudang.
                                    </div>

                                @elseif($po->status == 'Selesai')
                                    <div class="alert alert-success py-2 px-3 mb-2">
                                        PO telah selesai.
                                    </div>
                                @endif

                               <select name="status"
                                        class="form-control"
                                        {{ $po->status == 'Pending' || $po->status == 'Selesai' ? 'disabled' : '' }}>

                                    {{-- Pending --}}
                                    @if($po->status == 'Pending')
                                        <option selected>
                                            Menunggu Approval Owner
                                        </option>
                                    @endif

                                    {{-- Owner approve --}}
                                    @if($po->status == 'Disetujui')
                                        <option value="Diproses">
                                            Diproses
                                        </option>
                                    @endif

                                    {{-- Sedang diproses --}}
                                    @if($po->status == 'Diproses')
                                        <option value="Diproses" selected>
                                            Diproses
                                        </option>

                                        <option value="Selesai">
                                            Selesai
                                        </option>
                                    @endif

                                    {{-- Sudah selesai --}}
                                    @if($po->status == 'Selesai')
                                        <option selected>
                                            Selesai
                                        </option>
                                    @endif

                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="fw-semibold">
                                    Keterangan
                                </label>

                                <textarea name="keterangan"
                                          {{ $po->status == 'Selesai' ? 'disabled' : '' }}
                                          class="form-control"
                                          rows="4"
                                          placeholder="Masukkan keterangan progress PO">{{ $po->keterangan }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">

                    @if($po->status != 'Selesai')
                    <button type="submit"
                            class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save"></i>
                        Simpan
                    </button>
                    @endif

                    <a href="{{ url('gudang/po') }}"
                       class="btn btn-secondary px-4">

                        <i class="mdi mdi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>

.custom-scroll{
    overflow-x:auto;
}

.custom-table{
    min-width:950px;
    border-collapse:separate;
    border-spacing:0 8px;
}

.custom-table thead th{
    border:none !important;
    background:#f8fafc;
    color:#6b7280;
    font-size:13px;
    font-weight:600;
    padding:12px 14px;
    white-space:nowrap;
}

.custom-table tbody tr{
    background:#fff;
    box-shadow:0 1px 8px rgba(0,0,0,0.04);
    border-radius:12px;
}

.custom-table tbody td{
    padding:12px 14px;
    vertical-align:middle;
    border-top:none !important;
    border-bottom:none !important;
    font-size:14px;
}

.custom-table tbody tr td:first-child{
    border-top-left-radius:12px;
    border-bottom-left-radius:12px;
}

.custom-table tbody tr td:last-child{
    border-top-right-radius:12px;
    border-bottom-right-radius:12px;
}

.info-card{
    background:#f8fafc;
    border-radius:12px;
    padding:14px;
}

.info-card small{
    color:#6b7280;
    font-size:12px;
}

.info-card h6{
    font-size:15px;
}

.produk-box{
    background:#f8fafc;
    padding:8px 10px;
    border-radius:10px;
    font-size:14px;
}

.produk-total-box{
    background:#eff6ff;
    color:#2563eb;
    border-radius:10px;
    padding:8px 10px;
    text-align:center;
    font-weight:600;
    font-size:15px;
    line-height:1.1;
    display:inline-block;
    min-width:70px;
}

.produk-total-box small{
    display:block;
    margin-top:2px;
    color:#64748b;
    font-size:10px;
    font-weight:500;
}

.qty-badge{
    background:#eef2ff;
    color:#4338ca;
    padding:5px 12px;
    border-radius:30px;
    font-size:13px;
    font-weight:600;
}

.status-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:10px 18px;
    border-radius:999px;
    font-size:14px;
    font-weight:700;
    min-width:110px;
}

.status-badge.warning{
    background:#fff7ed;
    color:#ea580c;
}

.status-badge.info{
    background:#eff6ff;
    color:#2563eb;
}

.status-badge.success{
    background:#ecfdf5;
    color:#059669;
}

.status-badge.approved{
    background:#ecfeff;
    color:#0891b2;
}

.form-control{
    border-radius:10px;
    min-height:42px;
    font-size:14px;
}

textarea.form-control{
    min-height:95px;
}

.btn{
    border-radius:10px;
    font-size:14px;
    padding:9px 16px;
}

</style>
@endsection