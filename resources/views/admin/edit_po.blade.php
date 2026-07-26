@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
   <div style="background:white;padding:22px 28px;border-radius:18px;margin-bottom:25px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;box-shadow:0 4px 15px rgba(0,0,0,.05);border:1px solid #7a2323;">
        <div>
            <h2 style="font-weight:700;margin-bottom:5px;color:#2c2c2c;">
                Edit Purchase Order
            </h2>
            <p style="color:#777;margin:0;">
                Perbarui data PO selama status masih Pending.
            </p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <a href="{{ url('admin/po') }}"
                class="btn btn-light"
                style="height:46px;min-width:120px;background:#7a2323;color:#fff;border-radius:12px;font-weight:600;display:flex;align-items:center;justify-content:center;">
                <i class="mdi mdi-arrow-left mr-1"></i>
                Kembali
            </a>
            <button type="submit"
                    form="formEditPO"
                    class="btn"
                    style="height:46px;background:#16a34a;color:white;border-radius:12px;font-weight:600;padding:0 22px;">
                <i class="mdi mdi-content-save-outline mr-1"></i>
                Update Purchase Order
            </button>
        </div>
    </div>

    <form id="formEditPO"
          action="{{ url('admin/po/update/' . $po->id) }}"
        method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8">
                <div class="po-card">

                    <h5 class="section-title">
                        Informasi Customer
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Customer
                            </label>

                            <input type="text"
                                   name="customer"
                                   class="form-control input-custom"
                                   value="{{ $po->customer }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                No HP
                            </label>
                            <input type="text"
                                    name="no_hp"
                                    class="form-control input-custom"
                                    value="{{ $po->no_hp }}">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="label-custom">
                                Alamat
                            </label>
                            <textarea name="alamat"
                                      class="form-control input-custom"
                                      rows="3">{{ $po->alamat }}</textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="label-custom">
                                Estimasi Awal
                            </label>

                            <input type="date"
                                   name="estimasi_awal"
                                   class="form-control input-custom"
                                   value="{{ $po->estimasi_awal }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="label-custom">
                                Estimasi Akhir
                            </label>

                            <input type="date"
                                   name="estimasi_akhir"
                                   class="form-control input-custom"
                                   value="{{ $po->estimasi_akhir }}">
                        </div>
                    </div>
                </div>

                @foreach($po->detail as $index => $detail)

                <div class="po-card">
                    <div class="produk-header">
                        <div class="produk-number">
                            {{ $index + 1 }}
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Produk {{ $index + 1 }}
                            </h5>

                            <small class="text-muted">
                                Detail produk purchase order
                            </small>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Nama Produk
                            </label>
                            <input type="text"
                                    name="produk[]"
                                    class="form-control input-custom"
                                    value="{{ $detail->produk }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Foto Produk
                            </label>
                            <div class="foto-wrapper">
                                @if($detail->foto)
                                    <img src="{{ asset('assets/foto/po/'.$detail->foto) }}"
                                        class="preview-foto">
                                @else
                                    <div class="text-muted">
                                        Tidak ada foto
                                    </div>
                                @endif
                                <input type="file"
                                        name="foto[]"
                                        class="form-control input-custom"
                                        accept="image/*">
                            </div>
                            <small class="text-muted">
                                Kosongkan jika tidak ingin mengganti foto
                            </small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="label-custom">
                                Qty
                            </label>

                            <input type="number"
                                   name="qty[]"
                                   class="form-control input-custom"
                                   min="1"
                                   value="{{ $detail->qty }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="label-custom">
                                HPP Estimasi Admin
                            </label>
                            @if($po->status == 'Pending')
                                <input type="text"
                                       name="hpp_estimasi_admin[]"
                                       class="form-control input-custom rupiah"
                                       value="{{ $detail->hpp_estimasi_admin ? number_format($detail->hpp_estimasi_admin,0,',','.') : '' }}"                                       oninput="formatRupiah(this)">
                            @else
                                <input type="text"
                                       class="form-control readonly-input"
                                       value="Rp {{ number_format($detail->hpp_estimasi_admin ?? 0,0,',','.') }}"
                                       readonly>
                            @endif
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="label-custom">
                                HPP Estimasi Gudang
                            </label>
                            <input type="text"
                                   class="form-control readonly-input"
                                   value="{{ $detail->hpp_estimasi_gudang ? number_format($detail->hpp_estimasi_gudang,0,',','.') : '-' }}"
                                   readonly>
                        </div>

                        {{-- Harga jual readonly --}}
                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Harga Jual
                            </label>

                            <input type="text"
                                   class="form-control readonly-input"
                                   value="{{ $detail->harga_jual ? 'Rp '.number_format($detail->harga_jual,0,',','.') : 'Menunggu Owner' }}"
                                   readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Deskripsi Produk
                            </label>

                            <textarea name="deskripsi[]"
                                      class="form-control input-custom"
                                      rows="4"
                                      placeholder="Masukkan deskripsi produk">{{ $detail->deskripsi }}</textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="col-lg-4">
                <div class="summary-card">
                    <h5 class="fw-bold mb-4">
                        Informasi Tambahan
                    </h5>

                    <div class="mb-3">
                        <label class="label-custom">
                            DP Customer
                        </label>
                        @if($po->status == 'Pending')
                            <input type="text"
                                   name="dp"
                                   class="form-control input-custom rupiah"
                                   value="{{ $po->dp ? number_format($po->dp,0,',','.') : '' }}"
                                   oninput="formatRupiah(this)">
                        @else
                            <div class="readonly-input p-3">
                                Rp {{ number_format($po->dp ?? 0,0,',','.') }}
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="label-custom">
                            Metode Pembayaran
                        </label>
                        @if($po->status == 'Pending')
                            <select name="metode_pembayaran"
                                    class="form-control input-custom">
                                <option value="Tunai"
                                    {{ $po->metode_pembayaran == 'Tunai' ? 'selected':'' }}>
                                    Tunai
                                </option>
                                <option value="Transfer Bank"
                                    {{ $po->metode_pembayaran == 'Transfer Bank' ? 'selected':'' }}>
                                    Transfer Bank
                                </option>
                                <option value="QRIS"
                                    {{ $po->metode_pembayaran == 'QRIS' ? 'selected':'' }}>
                                    QRIS
                                </option>
                            </select>
                        @else
                            <div class="readonly-input p-3">
                                {{ $po->metode_pembayaran ?? '-' }}
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="label-custom">
                            Status Pembayaran
                        </label>
                        @if($po->status == 'Pending')
                            <select name="status_pembayaran"
                                    class="form-control input-custom">
                                <option value="DP"
                                    {{ $po->status_pembayaran == 'DP' ? 'selected' : '' }}>
                                    DP
                                </option>
                                <option value="Lunas"
                                    {{ $po->status_pembayaran == 'Lunas' ? 'selected' : '' }}>
                                    Lunas
                                </option>
                            </select>
                        @else
                            <input type="text"
                                   class="form-control readonly-input"
                                   value="{{ $po->status_pembayaran ?? '-' }}"
                                   readonly>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="label-custom">
                            Keterangan
                        </label>

                        <div class="readonly-input p-3">
                            {{ $po->keterangan ?? 'Belum ada keterangan' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="label-custom">
                            Status PO
                        </label>

                        <input type="text"
                               class="form-control readonly-input"
                               value="{{ $po->status }}"
                               readonly>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function formatRupiah(input) {
    let value = input.value.replace(/\D/g,'');
    if(value){
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }else{
        input.value = '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.rupiah').forEach(function(input){
        formatRupiah(input);
    });
});

document.querySelector('form').addEventListener('submit', function () {
    document.querySelectorAll('.rupiah').forEach(function(input){
        input.value = input.value.replace(/\./g,'');
    });
});
</script>

<style>

.po-card{
    background:white;
    border-radius:20px;
    padding:25px;
    margin-bottom:20px;
    border:1px solid #ececec;
    box-shadow:0 2px 12px rgba(0,0,0,.04);
}

.summary-card{
    background:white;
    border-radius:20px;
    padding:25px;
    position:sticky;
    top:20px;
    border:1px solid #ececec;
    box-shadow:0 2px 12px rgba(0,0,0,.04);
}

.section-title{
    font-weight:700;
    margin-bottom:22px;
}

.label-custom{
    font-weight:600;
    margin-bottom:8px;
    display:block;
    color:#374151;
}

.input-custom{
    border-radius:12px;
    min-height:48px;
    border:1px solid #dcdcdc;
}

.input-custom:focus{
    box-shadow:none;
    border-color:#4B49AC;
}

.readonly-input{
    border-radius:12px;
    min-height:48px;
    background:#f5f7fb;
    border:1px solid #e5e7eb;
    color:#6b7280;
}

.produk-header{
    display:flex;
    align-items:center;
    gap:15px;
}

.produk-number{
    width:50px;
    height:50px;
    border-radius:14px;
    background:#4B49AC;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:18px;
}

.btn-save{
    width:100%;
    height:52px;
    border:none;
    border-radius:14px;
    background:#16a34a;
    color:white;
    font-weight:600;
    font-size:15px;
    transition:.2s;
}

.btn-save:hover{
    background:#15803d;
}

textarea{
    resize:none;
}

.foto-wrapper{
    display:flex;
    align-items:center;
    gap:15px;
    padding:10px;
    background:#f8f9fa;
    border-radius:12px;
    border:1px solid #e5e7eb;
}

.preview-foto{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:12px;
    border:1px solid #ddd;
}

</style>
@endsection