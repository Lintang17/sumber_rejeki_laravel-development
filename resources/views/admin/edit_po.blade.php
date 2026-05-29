@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;gap:15px;flex-wrap:wrap;">

    <div>
        <h2 style="font-weight:700;margin-bottom:5px;color:#2c2c2c;">
            Edit Purchase Order
        </h2>

        <p style="color:#777;margin:0;">
            Edit data purchase order selama status masih pending.
        </p>
    </div>

    <div style="display:flex;gap:10px;align-items:center;">

        <a href="{{ url('admin/po') }}"
           class="btn btn-light shadow-sm"
           style="border-radius:12px;padding:10px 18px;white-space:nowrap;">
            <i class="mdi mdi-arrow-left"></i>
            Kembali
        </a>

    </div>

</div>

    <form action="{{ url('admin/po/update/' . $po->id) }}"
          method="POST">

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
                                HPP Estimasi
                            </label>

                            <input type="number"
                                   class="form-control readonly-input"
                                   value="{{ $detail->hpp_estimasi }}"
                                   readonly>
                        </div>

                        {{-- Harga jual readonly --}}
                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Harga Jual
                            </label>

                            <input type="number"
                                   class="form-control readonly-input"
                                   value="{{ $detail->harga_jual }}"
                                   readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="label-custom">
                                Deskripsi
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
                            Keterangan
                        </label>

                        <textarea class="form-control readonly-input"
                                  rows="5"
                                  readonly>{{ $po->keterangan ?? '-' }}</textarea>
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

                    <button type="submit"
                            class="btn-save">
                        <i class="mdi mdi-content-save-outline"></i>
                        Update Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

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

</style>
@endsection