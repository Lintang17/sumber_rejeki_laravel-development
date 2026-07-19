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

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Terjadi kesalahan:</strong>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<script>
    const isOwner = @json($isOwner);
</script>

<div class="content-wrapper" style="padding-left:25px;padding-right:25px;">
    <div style="background:white;padding:22px 28px;border-radius:18px;margin-bottom:25px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;box-shadow:0 4px 15px rgba(0,0,0,.05);border:1px solid #7a2323;">
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
                    <div style="background:white;border-radius:18px;padding:24px;margin-bottom:20px;border:1px solid #7e0c0c;box-shadow:0 2px 10px rgba(0,0,0,.04);">
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
                                        class="form-control"
                                        required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    Estimasi Akhir
                                </label>

                                <input type="date" 
                                        name="estimasi_akhir" 
                                        class="form-control"
                                        required>
                            </div>
                        </div>
                    </div>
                    <div class="produk-card" style="background:white;border-radius:18px;padding:24px;margin-bottom:20px;border:1px solid #760808;box-shadow:0 2px 10px rgba(0,0,0,.04);">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                            <div style="display:flex;align-items:center;">
                                <div style="width:45px;height:45px;border-radius:12px;background:#4B49AC;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;margin-right:14px;">1</div>
                                <div>
                                    <h5 style="margin:0;font-weight:700;">Produk Baru</h5>
                                    <small style="color:#777;">Isi informasi produk</small>
                                </div>
                            </div>
                            <div style="display:flex;gap:10px;">
                                <button type="button"
                                    onclick="addProduk()"
                                    style="display:flex;align-items:center;gap:8px;border:none;border-radius:12px;background:#4B49AC;color:white;padding:10px 16px;font-weight:600;font-size:14px;cursor:pointer;">
                                    <i class="mdi mdi-plus" style="font-size:18px;"></i>
                                    <span>Tambah Produk</span>
                                </button>
                                <button type="button"
                                    onclick="removeProduk(this)"
                                    style="display:flex;align-items:center;gap:8px;border:none;border-radius:12px;background:#fff1f1;color:#dc3545;padding:10px 16px;font-weight:600;font-size:14px;cursor:pointer;">
                                    <i class="mdi mdi-delete" style="font-size:18px;"></i>
                                    <span>Hapus Produk</span>
                                </button>
                            </div>
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
                                            onchange="previewFoto(this)"
                                            required>
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
                                        value=""
                                        oninput="calculateTotal()"
                                        required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    HPP Estimasi Admin
                                </label>

                                @if(auth()->user()->role == 'Admin')
                                    <input type="text"
                                            name="hpp_estimasi_admin[]"
                                            class="form-control harga-admin"
                                            value=""
                                            oninput="formatRupiah(this); calculateTotal()"
                                            required>
                                @else
                                    <input type="text"
                                            name="hpp_estimasi_admin[]"
                                            class="form-control"
                                            value=""
                                            readonly>
                                @endif
                            </div>

                            <div class="col-md-3 mb-3">
                                <label style="font-weight:600;">
                                    HPP Estimasi Gudang
                                </label>

                                @if(auth()->user()->role == 'Gudang')
                                    <input type="text"
                                            name="hpp_estimasi_gudang[]"
                                            class="form-control harga-gudang"
                                            value=""
                                            oninput="formatRupiah(this); calculateTotal()"
                                            required>            
                                @else
                                    <input type="text"
                                            name="hpp_estimasi_gudang[]"
                                            class="form-control"
                                            value=""
                                            readonly>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label style="font-weight:600;">
                                    Harga Jual
                                </label>

                                @if($isOwner)
                                   <input type="text"
                                            name="harga_jual[]"
                                            class="form-control harga-jual"
                                            value=""
                                            oninput="formatRupiah(this); calculateTotal()"
                                            required>
                                @else
                                    <input type="hidden"
                                            name="harga_jual[]"
                                            value="">

                                    <input type="text"
                                            class="form-control"
                                            disabled
                                            value="">
                                @endif
                            </div>

                            <div class="col-md-12 mb-3">
                                <label style="font-weight:600;">
                                    Deskripsi Produk
                                </label>

                                <textarea name="deskripsi[]"
                                          class="form-control"
                                          rows="2"
                                          placeholder="Tambahkan detail produk..."
                                          required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div style="background:white;border-radius:18px;padding:25px;position:sticky;top:20px;border:1px solid #7e0c0c;box-shadow:0 2px 12px rgba(0,0,0,.04);">
                    <h5 style="font-weight:700;margin-bottom:25px;">Ringkasan PO</h5>
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Total Produk</span>
                        <strong id="totalProduk">1</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Total Qty</span>
                        <strong id="totalQty">0</strong>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">
                        <span>Total Harga</span>
                        <strong>Menunggu Owner</strong>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label style="font-weight:600">
                            Nominal Pembayaran
                        </label>

                        <input type="text"
                            name="dp"
                            id="dp"
                            class="form-control"
                            value=""
                            min="0"
                            required
                            oninput="formatRupiah(this); calculateTotal()"> 
                    </div>

                    <div class="mb-3">
                        <label style="font-weight:600">
                            Metode Pembayaran
                        </label>

                        <select name="metode_pembayaran"
                                class="form-control"
                                required>
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
                                class="form-control"
                                required>
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
            <input type="text"
                name="hpp_estimasi_admin[]"
                class="form-control harga-admin"
                value=""
                oninput="formatRupiah(this); calculateTotal()"
                required>
        `;
    } else {
        hppAdminInput = `
            <input type="text"
                name="hpp_estimasi_admin[]"
                class="form-control"
                value=""
                readonly>
        `;
    }

    if(role === 'Gudang'){
        hppGudangInput = `
            <input type="text"
                name="hpp_estimasi_gudang[]"
                class="form-control harga-gudang"
                value=""
                oninput="formatRupiah(this); calculateTotal()"
                required>
        `;
    } else {
        hppGudangInput = `
            <input type="text"
                name="hpp_estimasi_gudang[]"
                class="form-control"
                value=""
                readonly>
        `;
    }

    if(isOwner){
        hargaJualInput = `
            <input type="text"
                name="harga_jual[]"
                class="form-control harga-jual"
                value=""
                oninput="formatRupiah(this); calculateTotal()"
                required>
        `;
    } else {
        hargaJualInput = `
            <input type="hidden"
                name="harga_jual[]"
                value="">

            <input type="text"
                class="form-control"
                value=""
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

            <div style="display:flex;gap:10px;">
                <button type="button"
                    onclick="addProduk()"
                    style="display:flex;align-items:center;gap:8px;border:none;border-radius:12px;background:#4B49AC;color:white;padding:10px 16px;font-weight:600;cursor:pointer;">
                    <i class="mdi mdi-plus"></i>
                    Tambah Produk
                </button>
                <button type="button"
                    onclick="removeProduk(this)"
                    style="display:flex;align-items:center;gap:8px;border:none;border-radius:12px;background:#fff1f1;color:#dc3545;padding:10px 16px;font-weight:600;cursor:pointer;">
                    <i class="mdi mdi-delete"></i>
                    Hapus Produk
                </button>
            </div>
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
                        onchange="previewFoto(this)"
                        required>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label>Qty</label>
                <input type="number"
                    name="qty[]"
                    class="form-control qty"
                    min="1"
                    value=""
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
                    placeholder="Tambahkan detail produk..."required></textarea>
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
        Swal.fire({
            icon: 'warning',
            title: 'Tidak dapat menghapus',
            text: 'Minimal harus ada 1 produk.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#4B49AC'
        });
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

function formatRupiah(input){

    let value = input.value.replace(/\D/g,''); // hapus selain angka

    if(value){
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }else{
        input.value = '';
    }
}

// sebelum submit ubah kembali jadi angka
document.querySelector('form').addEventListener('submit', function(){
    document.querySelectorAll('.harga-admin, .harga-gudang, .harga-jual, #dp')
    .forEach(input => {
        input.value = input.value.replace(/\./g,'');
    });

});

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