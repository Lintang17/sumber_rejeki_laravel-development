@extends('layouts.admin')

@section('content')
<style>
input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button{
    -webkit-appearance:none;
    margin:0;
}
input[type=number]{
    -moz-appearance:textfield;
}
.foto-wrapper{
    display:flex;
    align-items:center;
    gap:15px;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:12px;
    background:#fafafa;
}

.preview-foto{
    width:80px;
    height:80px;
    object-fit:cover;
    border-radius:12px;
    border:1px solid #ddd;
    background:#fff;
    flex-shrink:0;
}

.foto-input{
    border-radius:12px !important;
}
</style>

@php
    $isOwner = auth()->user()->role === 'Owner';
@endphp

<script>
    const isOwner = @json($isOwner);
</script>

<div class="content-wrapper">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:15px;">
        <div>
            <h2 style="font-weight:700;margin-bottom:5px;color:#2c2c2c;">Tambah PO</h2>
            <p style="color:#777;margin:0;">Tambahkan produk dan pantau total pesanan secara realtime.</p>
        </div>
        <button type="button" onclick="addProduk()" style="border:none;background:#4B49AC;color:white;padding:12px 20px;border-radius:12px;font-weight:600;cursor:pointer;">
            <i class="mdi mdi-plus"></i>
            Tambah Produk
        </button>
    </div>

    <form action="{{ url('admin/po/store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div id="produk-wrapper">
                    <div style="background:white;border-radius:18px;padding:24px;margin-bottom:20px;border:1px solid #ececec;box-shadow:0 2px 10px rgba(0,0,0,.04);">
                        <h5 style="font-weight:700;margin-bottom:20px;">
                            Informasi Customer
                        </h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label style="font-weight:600;">
                                    Customer
                                </label>

                                <input type="text"
                                        name="customer"
                                        class="form-control"
                                        placeholder="Masukkan nama customer"
                                        required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label style="font-weight:600;">
                                    No HP
                                </label>

                                <input type="text"
                                        name="no_hp"
                                        class="form-control"
                                        placeholder="08xxxxxxxxxx"
                                        inputmode="numeric"
                                        pattern="[0-9]+"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        maxlength="15"
                                        required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label style="font-weight:600;">
                                    Alamat
                                </label>

                                <textarea name="alamat"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Masukkan alamat customer"
                                        required></textarea>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    Estimasi Awal
                                </label>

                                <input type="date"
                                        name="estimasi_awal"
                                        class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    Estimasi Akhir
                                </label>

                                <input type="date" 
                                        name="estimasi_akhir" 
                                        class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="produk-card" style="background:white;border-radius:18px;padding:24px;margin-bottom:20px;border:1px solid #ececec;box-shadow:0 2px 10px rgba(0,0,0,.04);">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                            <div style="display:flex;align-items:center;">
                                <div style="width:45px;height:45px;border-radius:12px;background:#4B49AC;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;margin-right:14px;">1</div>
                                <div>
                                    <h5 style="margin:0;font-weight:700;">Produk Baru</h5>
                                    <small style="color:#777;">Isi informasi produk</small>
                                </div>
                            </div>
                            <button type="button" onclick="removeProduk(this)" style="display:flex;align-items:center;gap:8px;border:none;border-radius:12px;background:#fff1f1;color:#dc3545;padding:10px 16px;font-weight:600;font-size:14px;cursor:pointer;transition:all .2s ease;box-shadow:0 2px 6px rgba(220,53,69,.12);" onmouseover="this.style.background='#dc3545';this.style.color='white';this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 14px rgba(220,53,69,.25)';" onmouseout="this.style.background='#fff1f1';this.style.color='#dc3545';this.style.transform='translateY(0)';this.style.boxShadow='0 2px 6px rgba(220,53,69,.12)';">
                                <i class="mdi mdi-delete" style="font-size:18px;"></i>
                                <span>Hapus Produk</span>
                            </button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label style="font-weight:600;">
                                    Nama Produk
                                </label>
                                <input type="text"
                                        name="produk[]"
                                        class="form-control"
                                        placeholder="Masukkan nama produk"
                                        required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label style="font-weight:600;">
                                    Foto Produk
                                </label>

                                <div class="foto-wrapper">
                                    <img src="https://via.placeholder.com/80"
                                        class="preview-foto">

                                    <input type="file"
                                            name="foto[]"
                                            class="form-control foto-input"
                                            accept="image/*"
                                            onchange="previewFoto(this)">
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    Qty
                                </label>
                                <input type="number"
                                        name="qty[]"
                                        class="form-control qty"
                                        min="1"
                                        value="1"
                                        oninput="calculateTotal()"
                                        required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    HPP Estimasi Admin
                                </label>

                                @if(auth()->user()->role == 'Admin')
                                    <input type="number"
                                            name="hpp_estimasi_admin[]"
                                            class="form-control harga-admin"
                                            min="0"
                                            value="0"
                                            oninput="calculateTotal()">
                                @else
                                    <input type="number"
                                            name="hpp_estimasi_admin[]"
                                            class="form-control"
                                            value="0"
                                            readonly>
                                @endif
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    HPP Estimasi Gudang
                                </label>

                                @if(auth()->user()->role == 'Gudang')
                                    <input type="number"
                                            name="hpp_estimasi_gudang[]"
                                            class="form-control harga-gudang"
                                            min="0"
                                            value="0"
                                            oninput="calculateTotal()">
                                @else
                                    <input type="number"
                                            name="hpp_estimasi_gudang[]"
                                            class="form-control"
                                            value="0"
                                            readonly>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label style="font-weight:600;">
                                    Harga Jual
                                </label>

                                @if($isOwner)
                                   <input type="number"
                                            name="harga_jual[]"
                                            class="form-control harga-jual"
                                            min="0"
                                            value="0"
                                            oninput="calculateTotal()">
                                @else
                                    <input type="hidden"
                                            name="harga_jual[]"
                                            value="0">

                                    <input type="number"
                                            class="form-control"
                                            disabled
                                            value="0">
                                @endif
                            </div>

                            <div class="col-md-12 mb-3">
                                <label style="font-weight:600;">
                                    Deskripsi Produk
                                </label>

                                <textarea name="deskripsi[]"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Tambahkan detail produk..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div style="background:white;border-radius:18px;padding:25px;position:sticky;top:20px;border:1px solid #ececec;box-shadow:0 2px 12px rgba(0,0,0,.04);">
                    <h5 style="font-weight:700;margin-bottom:25px;">Ringkasan PO</h5>
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Total Produk</span>
                        <strong id="totalProduk">1</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Total Qty</span>
                        <strong id="totalQty">1</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Total PO</span>
                        <strong>Menunggu Owner</strong>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label style="font-weight:600">
                            Nominal Pembayaran
                        </label>

                        <input type="number"
                            name="dp"
                            id="dp"
                            class="form-control"
                            value="0"
                            min="0"
                            oninput="calculateTotal()"> 
                    </div>

                    <div class="mb-3">
                        <label style="font-weight:600">
                            Metode Pembayaran
                        </label>

                        <select name="metode_pembayaran"
                                class="form-control">
                            <option value="">Pilih Metode</option>
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                   <div class="mb-3">
                        <label style="font-weight:600">
                            Status Pembayaran
                        </label>

                        <select name="status_pembayaran"
                                id="statusPembayaran"
                                class="form-control">
                            <option value="DP">DP</option>
                            <option value="Lunas">Lunas</option>
                        </select>
                    </div>

                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Sisa Pembayaran</span>
                        <strong>Belum dapat dihitung</strong>
                    </div>
                    <button type="submit" style="width:100%;height:50px;border:none;border-radius:12px;background:#28a745;color:white;font-weight:600;cursor:pointer;">
                        <i class="mdi mdi-content-save-outline"></i>
                        Simpan Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
let produkIndex = 1;

function addProduk(){
    produkIndex++;

    let role = "{{ auth()->user()->role }}";
    let hppAdminInput = '';
    let hppGudangInput = '';
    let hargaJualInput = '';

    if(role === 'Admin'){
        hppAdminInput = `
            <input type="number"
                name="hpp_estimasi_admin[]"
                class="form-control harga-admin"
                min="0"
                value="0"
                oninput="calculateTotal()">
        `;
    } else {
        hppAdminInput = `
            <input type="number"
                name="hpp_estimasi_admin[]"
                class="form-control"
                value="0"
                readonly>
        `;
    }

    if(role === 'Gudang'){
        hppGudangInput = `
            <input type="number"
                name="hpp_estimasi_gudang[]"
                class="form-control harga-gudang"
                min="0"
                value="0"
                oninput="calculateTotal()">
        `;
    } else {
        hppGudangInput = `
            <input type="number"
                name="hpp_estimasi_gudang[]"
                class="form-control"
                value="0"
                readonly>
        `;
    }

    if(isOwner){
        hargaJualInput = `
            <input type="number"
                name="harga_jual[]"
                class="form-control harga-jual"
                min="0"
                value="0"
                oninput="calculateTotal()">
        `;
    } else {
        hargaJualInput = `
            <input type="hidden"
                name="harga_jual[]"
                value="0">

            <input type="number"
                class="form-control"
                value="0"
                disabled>
        `;
    }

    let html = `
    <div class="produk-card" style="background:white;border-radius:18px;padding:24px;margin-bottom:20px;border:1px solid #ececec;box-shadow:0 2px 10px rgba(0,0,0,.04);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <div style="display:flex;align-items:center;">
                <div style="width:45px;height:45px;border-radius:12px;background:#4B49AC;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;margin-right:14px;">
                    ${produkIndex}
                </div>
                <div>
                    <h5 style="margin:0;font-weight:700;">Produk Baru</h5>
                    <small style="color:#777;">Isi informasi produk</small>
                </div>
            </div>

            <button type="button"
                onclick="removeProduk(this)"
                style="display:flex;align-items:center;gap:8px;border:none;border-radius:12px;background:#fff1f1;color:#dc3545;padding:10px 16px;font-weight:600;cursor:pointer;">
                <i class="mdi mdi-delete"></i>
                Hapus Produk
            </button>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nama Produk</label>
                <input type="text"
                    name="produk[]"
                    class="form-control"
                    placeholder="Masukkan nama produk"
                    required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Foto Produk</label>

                <div class="foto-wrapper">
                    <img src="https://via.placeholder.com/80"
                        class="preview-foto">

                    <input type="file"
                        name="foto[]"
                        class="form-control foto-input"
                        accept="image/*"
                        onchange="previewFoto(this)">
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label>Qty</label>
                <input type="number"
                    name="qty[]"
                    class="form-control qty"
                    min="1"
                    value="1"
                    oninput="calculateTotal()"
                    required>
            </div>

            <div class="col-md-3 mb-3">
                <label>HPP Estimasi Admin</label>
                ${hppAdminInput}
            </div>

            <div class="col-md-3 mb-3">
                <label>HPP Estimasi Gudang</label>
                ${hppGudangInput}
            </div>

            <div class="col-md-6 mb-3">
                <label>Harga Jual</label>
                ${hargaJualInput}
            </div>

            <div class="col-md-12 mb-3">
                <label>Deskripsi Produk</label>
                <textarea name="deskripsi[]"
                    class="form-control"
                    rows="2"
                    placeholder="Tambahkan detail produk..."></textarea>
            </div>
        </div>
    </div>
    `;

    document.getElementById('produk-wrapper')
        .insertAdjacentHTML('beforeend', html);

    // update ringkasan
    let produkCount = document.querySelectorAll('.produk-card').length;
    document.getElementById('totalProduk').innerText = produkCount;
}

function removeProduk(button){
    let cards = document.querySelectorAll('.produk-card');
    if(cards.length <= 1){
        alert('Minimal 1 produk');
        return;
    }
    button.closest('.produk-card').remove();
    calculateTotal();
}

function previewFoto(input){
    const file = input.files[0];

    if(file){
        const reader = new FileReader();

        reader.onload = function(e){
            input.closest('.foto-wrapper')
                 .querySelector('.preview-foto')
                 .src = e.target.result;
        }

        reader.readAsDataURL(file);
    }
}

function calculateTotal(){

    let qtys = document.querySelectorAll('.qty');

    let totalQty = 0;

    qtys.forEach(qty => {
        totalQty += parseInt(qty.value) || 0;
    });

    let produkCount = document.querySelectorAll('.produk-card').length;

    document.getElementById('totalProduk').innerText = produkCount;
    document.getElementById('totalQty').innerText = totalQty;
}

calculateTotal();
</script>
@endsection