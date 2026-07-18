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
                        <small>Tanggal PO</small>
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
                                <th>Foto</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Jumlah Produk</th>
                                <th>Estimasi Awal</th>
                                <th>HPP Estimasi Admin</th>
                                <th>HPP Estimasi Gudang</th>
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
                                <td width="90">
                                    @if($detail->foto)
                                        <img src="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                             class="img-thumbnail"
                                             style="width:70px;height:70px;object-fit:cover;border-radius:10px;">
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                    @endif
                                </td>
                                <td style="min-width:220px;">
                                    {{ $detail->deskripsi ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <div class="qty-box">
                                        <div class="qty-number">
                                            {{ $detail->qty }}
                                        </div>
                                    </div>
                                </td>
                                @if($index==0)
                                <td rowspan="{{ $po->detail->count() }}"
                                    class="align-middle text-center">
                                    <div class="produk-total-box">
                                        <div class="total-number">
                                            {{ $po->detail->count() }}
                                        </div>
                                        <small>Produk</small>
                                    </div>
                                </td>
                                @endif
                                <td>
                                    @if($po->estimasi_awal)
                                        <span class="badge badge-success px-3 py-2">
                                            {{ \Carbon\Carbon::parse($po->estimasi_awal)->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($detail->hpp_estimasi_admin)
                                        <div class="hpp-admin">
                                            Rp {{ number_format($detail->hpp_estimasi_admin,0,',','.') }}
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="min-width:250px;">
                                    @if($po->status == 'Pending')
                                        <div style="background:#FFF7E6;border:2px dashed #F59E0B;border-radius:12px;padding:12px;">
                                            <label class="font-weight-bold mb-2 d-block" style="color:#B45309;">
                                                <i class="mdi mdi-pencil-circle"></i>
                                                HPP Estimasi Gudang
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group mb-2">
                                                <input type="text"
                                                       name="hpp_estimasi_gudang[{{ $detail->id }}]"
                                                       class="form-control rupiah"
                                                       placeholder="Contoh : 1.200.000"
                                                       value="{{ $detail->hpp_estimasi_gudang > 0 ? number_format($detail->hpp_estimasi_gudang,0,',','.') : '' }}"
                                                       required>
                                            </div>
                                            <small class="text-warning font-weight-bold">
                                                Wajib diisi oleh pihak Gudang sebelum PO disetujui Owner.
                                            </small>
                                        </div>
                                    @else
                                        @if($detail->hpp_estimasi_gudang > 0)
                                            <div style="background:#DCFCE7;color:#166534;padding:10px;border-radius:10px;font-weight:700;text-align:center;">
                                                Rp {{ number_format($detail->hpp_estimasi_gudang,0,',','.') }}
                                            </div>
                                        @else
                                            <span class="badge badge-danger px-3 py-2">
                                                Belum Diisi
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div style="background:#EFF6FF;color:#2563EB;padding:10px;border-radius:10px;font-weight:700;text-align:center;">
                                        Rp {{ number_format($detail->harga_jual,0,',','.') }}
                                    </div>
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
                                       {{ $po->status != 'Pending' ? 'disabled' : '' }}
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
                                        Gudang hanya dapat mengisi estimasi akhir, HPP estimasi gudang dan keterangan.
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
                                    @if($po->status=='Disetujui')
                                    <div class="alert alert-info">
                                        <b>PO telah disetujui Owner.</b><br>
                                        Gudang dapat memulai produksi dengan mengubah status menjadi
                                        <b>Diproses</b>.
                                    </div>
                                    <select name="status" class="form-control">
                                        <option value="Diproses">
                                            Diproses
                                        </option>
                                    </select>
                                    @endif

                                    {{-- Sedang diproses --}}
                                    @if($po->status=='Diproses')
                                    <div class="alert alert-primary">
                                        PO sedang diproses gudang.
                                    </div>
                                    <select name="status" class="form-control">
                                        <option value="Diproses" selected>Diproses</option>
                                        <option value="Diambil">Diambil</option>
                                    </select>
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
                                         class="form-control"
                                         rows="4"
                                         placeholder="Masukkan keterangan PO"
                                         @if($po->status == 'Pending')
                                            required
                                         @else
                                            disabled
                                         @endif>{{ $po->keterangan }}</textarea>
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
                       class="btn btn-secondary px-4"
                       style="background:#6c757d;border-color:#6c757d;color:#fff !important;">
                       <i class="mdi mdi-arrow-left" style="color:#fff !important;"></i>
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
    background:linear-gradient(180deg,#F4F8FF 0%, #9bb7e4 100%);
    color:#365486;
    font-size:13px;
    font-weight:700;
    padding:14px 16px;
    white-space:nowrap;
    border-top:1px solid #D6E4FF !important;
    border-bottom:2px solid #C5D8FF !important;
    border-left:none !important;
    border-right:none !important;
    letter-spacing:.3px;
}

.custom-table thead th:first-child{
    border-top-left-radius:12px;
}

.custom-table thead th:last-child{
    border-top-right-radius:12px;
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

<script>
document.querySelectorAll('.rupiah').forEach(function(input){
    input.addEventListener('input', function(){
        let angka = this.value.replace(/\D/g,'');
        if(angka === ''){
            this.value = '';
            return;
        }
        this.value = new Intl.NumberFormat('id-ID').format(angka);
    });
});
</script>
@endsection