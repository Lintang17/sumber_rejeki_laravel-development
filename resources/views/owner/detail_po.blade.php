@extends('layouts.admin')

@section('content')
<div class="content-wrapper">

    @php
        $disabled = !$bolehApprove || $po->status != 'Pending';

        $totalQty = $po->detail->sum('qty');
        $totalHpp = $po->detail->sum(function($item){
            return $item->hpp_estimasi * $item->qty;
        });

        $totalHargaJual = $po->detail->sum(function($item){
            return $item->harga_jual * $item->qty;
        });
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <div>
                    <h3 class="font-weight-bold mb-1">
                        Detail Purchase Order
                    </h3>

                    <p class="text-muted mb-0">
                        Review purchase order sebelum approve owner.
                    </p>
                </div>

                <div>
                    @if($po->status == 'Pending')
                        <span class="status-badge warning">
                            Pending
                        </span>

                    @elseif($po->status == 'Disetujui')
                        <span class="status-badge success">
                            Disetujui Owner
                        </span>

                    @elseif($po->status == 'Diproses')
                        <span class="status-badge info">
                            Diproses
                        </span>

                    @elseif($po->status == 'Selesai')
                        <span class="status-badge done">
                            Selesai
                        </span>

                    @elseif($po->status == 'Dibatalkan')
                        <span class="status-badge danger">
                            Dibatalkan
                        </span>
                    @endif
                </div>
            </div>

            <div class="row mb-4">

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Kode PO</small>
                        <h6>{{ $po->kode_po }}</h6>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Customer</small>
                        <h6>{{ $po->customer }}</h6>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Tanggal</small>
                        <h6>
                            {{ \Carbon\Carbon::parse($po->tanggal)->format('d M Y') }}
                        </h6>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Total Produk</small>
                        <h6>{{ $po->detail->count() }} Produk</h6>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Estimasi Awal</small>
                        <h6>
                            {{ $po->estimasi_awal
                                ? \Carbon\Carbon::parse($po->estimasi_awal)->format('d M Y')
                                : '-' }}
                        </h6>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Estimasi Akhir</small>
                        <h6>
                            {{ $po->estimasi_akhir
                                ? \Carbon\Carbon::parse($po->estimasi_akhir)->format('d M Y')
                                : '-' }}
                        </h6>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <div class="info-card">
                        <small>Keterangan Gudang</small>
                        <h6>
                            {{ $po->keterangan ?? '-' }}
                        </h6>
                    </div>
                </div>
            </div>

            @if(!$bolehApprove && $po->status == 'Pending')
                <div class="alert alert-warning">
                    Gudang harus mengisi estimasi akhir dan keterangan terlebih dahulu.
                </div>
            @endif

            <form action="{{ url('owner/po/' . $po->id . '/approve') }}"
                  method="POST">

                @csrf

                <div class="table-responsive">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Produk</th>
                                <th>Deskripsi</th>
                                <th>Qty</th>
                                <th>HPP Estimasi</th>
                                <th>Harga Jual</th>
                                <th>Subtotal HPP</th>
                                <th>Subtotal Jual</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($po->detail as $index => $detail)

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    {{ $detail->produk }}
                                </td>

                                <td>
                                    {{ $detail->deskripsi ?? '-' }}
                                </td>

                                <td>
                                    {{ $detail->qty }}
                                </td>

                                <td style="min-width:180px;">
                                    <input type="text"
                                           name="hpp_estimasi[]"
                                           class="form-control modern-input rupiah"
                                           value="{{ number_format($detail->hpp_estimasi,0,',','.') }}"
                                           {{ $disabled ? 'readonly' : '' }}
                                           inputmode="numeric"
                                           autocomplete="off"
                                           required>
                                </td>

                                <td style="min-width:180px;">
                                    <input type="text"
                                           name="harga_jual[]"
                                           class="form-control modern-input rupiah"
                                           value="{{ number_format($detail->harga_jual,0,',','.') }}"
                                           {{ $disabled ? 'readonly' : '' }}
                                           inputmode="numeric"
                                           autocomplete="off"
                                           required>
                                </td>

                                <td class="text-success fw-bold">
                                    Rp {{ number_format($detail->hpp_estimasi * $detail->qty,0,',','.') }}
                                </td>

                                <td class="text-primary fw-bold">
                                    Rp {{ number_format($detail->harga_jual * $detail->qty,0,',','.') }}
                                </td>
                            </tr>

                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">
                                    TOTAL
                                </th>

                                <th>
                                    {{ $totalQty }}
                                </th>

                                <th colspan="2"></th>

                                <th class="text-success">
                                    Rp {{ number_format($totalHpp,0,',','.') }}
                                </th>

                                <th class="text-primary">
                                    Rp {{ number_format($totalHargaJual,0,',','.') }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if(!$disabled)

                <div class="row mt-4">
                    <div class="col-md-4">

                        <label>Status PO</label>

                        <select name="status"
                                class="form-control modern-input"
                                required>

                            <option value="Approve">
                                Approve
                            </option>

                            <option value="Reject">
                                Reject
                            </option>

                        </select>
                    </div>
                </div>

                @endif

                <div class="mt-4">

                    @if(!$disabled)
                    <button type="submit"
                            class="btn btn-success">

                        Simpan Review
                    </button>
                    @endif

                    <a href="{{ url('owner/po') }}"
                       class="btn btn-secondary">

                        Kembali
                    </a>

                </div>
            </form>
        </div>
    </div>
</div>

<style>

.custom-table{
    width:100%;
    min-width:1200px;
}

.custom-table th{
    background:#f8fafc;
    white-space:nowrap;
}

.custom-table td,
.custom-table th{
    padding:14px;
    vertical-align:middle;
}

.info-card{
    background:#f8fafc;
    border-radius:12px;
    padding:14px;
    height:100%;
}

.info-card small{
    color:#6b7280;
}

.info-card h6{
    margin-top:5px;
    font-weight:700;
}

.modern-input{
    height: 45px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    padding: 8px 12px;
    background: #fff;
    font-size: 14px;
    transition: 0.2s;
}

.status-badge{
    padding:8px 14px;
    border-radius:20px;
    font-size:13px;
    font-weight:700;
}

.warning{
    background:#fff7ed;
    color:#ea580c;
}

.info{
    background:#dbeafe;
    color:#2563eb;
}

.success{
    background:#dcfce7;
    color:#15803d;
}

.done{
    background:#ecfccb;
    color:#4d7c0f;
}

.danger{
    background:#fee2e2;
    color:#dc2626;
}

</style>

<script>
function formatRupiah(angka) {
    let number_string = angka.replace(/[^0-9]/g, '').toString();

    if (!number_string) return '';

    return new Intl.NumberFormat('id-ID').format(number_string);
}

document.querySelectorAll('.rupiah').forEach(function(el) {

    el.addEventListener('input', function () {
        this.value = formatRupiah(this.value);
    });

    el.addEventListener('keydown', function (e) {
        if (
            !(
                (e.key >= '0' && e.key <= '9') ||
                ['Backspace','Delete','Tab','ArrowLeft','ArrowRight','Enter'].includes(e.key)
            )
        ) {
            e.preventDefault();
        }
    });

});

document.querySelector('form').addEventListener('submit', function () {

    document.querySelectorAll('input[name="hpp_estimasi[]"]').forEach(function (el) {
        el.value = el.value.replace(/[^0-9]/g, '');
    });

    document.querySelectorAll('input[name="harga_jual[]"]').forEach(function (el) {
        el.value = el.value.replace(/[^0-9]/g, '');
    });

});
</script>

@endsection