<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\OwnerController;
use Illuminate\Support\Facades\Route;



Route::get('/', [HomeController::class, 'index']);
Route::get('login', [AuthController::class, 'login']);
Route::post('loginproses', [AuthController::class, 'loginproses']);
Route::get('logout', [AuthController::class, 'logout']);

// -- ADMIN --//
Route::middleware(['auth'])->controller(AdminController::class)->group(function () {
    Route::post('update-profile-image', 'updateProfileImage');

    Route::get('admin', 'dashboard');

    // barang produksi
    Route::get('admin/produksidaftar', [AdminController::class, 'produksidaftar']);

    // showroom
    Route::get('admin/showroomtambah', [AdminController::class, 'showroomtambah']);
    Route::post('admin/showroomsimpan', [AdminController::class, 'showroomsimpan']);
    Route::get('admin/showroomdaftar', [AdminController::class, 'showroomdaftar']);

    // transaksi penjualan
    Route::get('admin/penjualandaftar', [AdminController::class, 'penjualandaftar']);
    Route::get('admin/penjualanedit/{id}', [AdminController::class, 'penjualanedit']);
    Route::post('admin/penjualanupdate/{id}', [AdminController::class, 'penjualanupdate']);
    // Cetak Nota & Faktur Penjualan (Admin)
    Route::get('admin/nota/{notajual}', [AdminController::class, 'Nota']);
    Route::get('admin/faktur/{notajual}', [AdminController::class, 'Faktur']);
    //Route::post('admin/ubahstatustransaksi', [AdminController::class, 'ubahStatusTransaksi']);

    // laporan
    //Route::get('admin/laporan/showroom', [AdminController::class, 'laporanshowroom'])->name('laporan.admin.showroom');
    //Route::get('admin/laporan/showroom/pdf', [AdminController::class, 'exportPdfShowroom'])->name('laporan.admin.showroom.pdf');
    //Route::get('admin/laporan/showroom/excel', [AdminController::class, 'exportExcelShowroom'])->name('laporan.admin.showroom.excel');

    Route::get('admin/laporan/penjualan', [AdminController::class, 'laporanpenjualan'])->name('laporan.admin.penjualan');
    Route::get('admin/laporan/penjualan/pdf', [AdminController::class, 'exportPdfPenjualan'])->name('laporan.admin.penjualan.pdf');
    Route::get('admin/laporan/penjualan/excel', [AdminController::class, 'exportExcelPenjualan'])->name('laporan.admin.penjualan.excel');



    // produk
    //Route::get('admin/produktambah', 'produktambah');
    //Route::post('admin/produksimpan', 'produksimpan');
    //Route::get('admin/produkdaftar', 'produkdaftar');
    //Route::get('admin/produkhapus/{id}', 'produkhapus');
    //Route::get('admin/produkedit/{id}', 'produkedit');
    //Route::put('admin/produkupdate/{id}', 'produkupdate');


    // barangmasuk
    //Route::get('admin/barangmasuktambah', [AdminController::class, 'barangmasuktambah']);
    //Route::post('admin/barangmasuksimpan', [AdminController::class, 'barangmasuksimpan']);
    //Route::get('admin/barangmasukdaftar', [AdminController::class, 'barangmasukdaftar']);
    //Route::get('admin/barangmasukedit/{id}', [AdminController::class, 'barangmasukedit']);
    //Route::put('admin/barangmasukupdate/{id}', [AdminController::class, 'barangmasukupdate']);
    //Route::get('admin/barangmasukhapus/{id}', [AdminController::class, 'barangmasukhapus']);
    //Route::post('admin/barangmasuk/update-status', [AdminController::class, 'updateStatusPembelian']);
    //Route::post('admin/barangmasuk-batalkan/{notabeli}', [AdminController::class, 'batalkanBarangMasuk']);



    // barangkeluar
    //Route::get('admin/barangkeluartambah', 'barangkeluartambah');
    //Route::post('admin/barangkeluarsimpan', 'barangkeluarsimpan');
    //Route::get('admin/barangkeluardaftar', 'barangkeluardaftar');
    //Route::get('admin/barangkeluaredit/{id}', 'barangkeluaredit');
    //Route::put('admin/barangkeluarupdate/{id}', 'barangkeluarupdate');
    //Route::get('admin/barangkeluarhapus/{id}', 'barangkeluarhapus');
    //Route::get('admin/cetaknota/{id}', 'cetaknota');
    //Route::get('admin/cetakfaktur/{id}', 'cetakfaktur');
    //Route::post('admin/ubahstatustransaksi', 'ubahstatustransaksi');
    //Route::post('admin/updatecustomproduk', 'updatecustomproduk');
    //Route::post('admin/ubahstatuspembelian', 'ubahstatuspembelian');

    /*// supplier
    Route::get('admin/supplier/daftar', [AdminController::class, 'supplierdaftar'])->name('supplier.daftar');
    Route::get('admin/supplier/tambah', [AdminController::class, 'suppliertambah'])->name('supplier.tambah');
    Route::post('admin/supplier/simpan', [AdminController::class, 'suppliersimpan'])->name('supplier.simpan');
    Route::get('admin/supplier/edit/{id}', [AdminController::class, 'supplieredit'])->name('supplier.edit');
    Route::put('admin/supplier/update/{id}', [AdminController::class, 'supplierupdate'])->name('supplier.update');
    Route::delete('admin/supplier/hapus/{id}', [AdminController::class, 'supplierhapus'])->name('supplier.hapus');

    // kategori
    Route::get('admin/kategori/daftar', [AdminController::class, 'kategoridaftar'])->name('kategori.daftar');
    Route::get('admin/kategori/tambah', [AdminController::class, 'kategoritambah'])->name('kategori.tambah');
    Route::post('admin/kategori/simpan', [AdminController::class, 'kategorisimpan'])->name('kategori.simpan');
    Route::get('admin/kategori/edit/{id}', [AdminController::class, 'kategoriedit'])->name('kategori.edit');
    Route::put('admin/kategori/update/{id}', [AdminController::class, 'kategoriupdate'])->name('kategori.update');
    Route::delete('admin/kategori/hapus/{id}', [AdminController::class, 'kategorihapus'])->name('kategori.hapus');
    */

    // stockopname
    // Route::get('admin/stockopnametambah', 'stockopnametambah');
    // Route::post('admin/stockopnamesimpan', 'stockopnamesimpan');
    // Route::get('admin/stockopnamedaftar', 'stockopnamedaftar');
    // Route::get('admin/stockopnameedit/{id}', 'stockopnameedit');
    // Route::put('admin/stockopnameupdate/{id}', 'stockopnameupdate');
    // Route::get('admin/stockopnamehapus/{id}', 'stockopnamehapus');

    // laporanpembelian
    //Route::get('admin/laporanpembelian', 'laporanpembelian');
    //Route::post('admin/laporanpembeliancetak', 'laporanpembelian');
    //Route::post('admin/laporanpembelianexcel', 'laporanpembelian');

    // laporanpenjualan
    //Route::get('admin/laporanpenjualan', 'laporanpenjualan');
    //Route::post('admin/laporanpenjualan', 'laporanpenjualan');
    //Route::get('admin/laporanpenjualancetak/{tahun}/{bulan}/{namabarang}', 'laporanpenjualancetak');
    //Route::get('admin/laporanpenjualanexcel/{tahun}/{bulan}/{namabarang}', 'laporanpenjualanexcel');

    // internal
    Route::get('admin/internaltambah', 'internaltambah');
    Route::post('admin/internalsimpan', 'internalsimpan');
    Route::get('admin/internaldaftar', 'internaldaftar');
    Route::get('admin/internaledit/{id}', 'internaledit');
    Route::put('admin/internalupdate/{id}', 'internalupdate');
    Route::delete('admin/internalhapus/{id}', 'internalhapus');

    // profile
    Route::get('admin/profile', 'profile');
    Route::put('admin/profileupdate', 'profileupdate');

    //Sistem PO
    Route::get('admin/po', 'detailPo');
    Route::get('admin/po/tambah', 'poTambah');
    Route::post('admin/po/store', 'poStore');

    Route::get('admin/po/edit/{id}', 'poEdit');
    Route::put('admin/po/update/{id}', 'poUpdate');
    Route::put('admin/po/status/{id}', 'updateStatusPo');
    Route::delete('admin/po/hapus/{id}', 'poHapus');
    Route::get('admin/po/print/{id}', 'poPrint');
});

// Route::middleware(['auth'])->controller(OwnerController::class)->group(function () {
//     Route::get('owner/stockopnamedaftar', 'stockopnamedaftar');
// });

// -- OWNER --//
Route::middleware(['auth'])->controller(OwnerController::class)->group(function () {
    Route::get('owner', 'dashboard');

    


    // barang produksi
    Route::get('owner/produksidaftar', [OwnerController::class, 'produksidaftar']);
    Route::get('owner/produksireview/{id}', [OwnerController::class, 'produksireview']); // Review satuan
    Route::post('owner/produksireview/{id}', [OwnerController::class, 'produksireviewproses']);
    //Route::get('owner/produksireview', [OwnerController::class, 'produksireviewlist']);

    // showroom
    Route::get('owner/showroomdaftar', [OwnerController::class, 'showroomdaftar']);

    // transaksi penjualan
    Route::get('owner/penjualandaftar', [OwnerController::class, 'penjualandaftar']);

    // stokopanem
    Route::get('owner/stokopname', [OwnerController::class, 'opnamedaftar'])->name('stokopname.daftar');
    //Route::get('owner/stokopname/tambah/{id}', [OwnerController::class, 'opnametambah'])->name('stokopname.tambah');
    //Route::post('owner/stokopname/simpan', [OwnerController::class, 'opnamesimpan'])->name('stokopname.simpan');
    Route::get('owner/stokopname/riwayat', [OwnerController::class, 'opnameriwayat'])->name('stokopname.riwayat');
    
    
    // produk
    //Route::get('owner/produktambah', 'produktambah');
    //Route::post('owner/produksimpan', 'produksimpan');
    //Route::get('owner/produkdaftar', 'produkdaftar');
    //Route::get('owner/produkedit/{id}', 'produkedit');
    //Route::put('owner/produkupdate/{id}', 'produkupdate');
    //Route::get('owner/produkhapus/{id}', 'produkhapus');

    // barangmasuk
    //Route::get('owner/barangmasuktambah', 'barangmasuktambah');
    //Route::post('owner/barangmasuksimpan', 'barangmasuksimpan');
    //Route::get('owner/barangmasukdaftar', 'barangmasukdaftar');
    //Route::get('owner/barangmasukedit/{id}', 'barangmasukedit');
    //Route::put('owner/barangmasukupdate/{id}', 'barangmasukupdate');
    // Route::get('owner/barangmasukhapus/{id}', 'barangmasukhapus');

    // barangkeluar
    //Route::get('owner/barangkeluartambah', 'barangkeluartambah');
    //Route::post('owner/barangkeluarsimpan', 'barangkeluarsimpan');
    //Route::get('owner/barangkeluardaftar', 'barangkeluardaftar');
    //Route::get('owner/barangkeluaredit/{id}', 'barangkeluaredit');
    //Route::put('owner/barangkeluarupdate/{id}', 'barangkeluarupdate');
    // Route::get('owner/barangkeluarhapus/{id}', 'barangkeluarhapus');
    //Route::get('owner/cetaknota/{id}', 'cetaknota');
    //Route::get('owner/cetakfaktur/{id}', 'cetakfaktur');
    // Route::post('owner/ubahstatustransaksi', 'ubahstatustransaksi');

    // stockopname
    // Route::get('owner/stockopnamedaftar', 'stockopnamedaftar');
    //Route::get('owner/stockopnametambah', 'stockopnametambah');
    //Route::post('owner/stockopnamesimpan', 'stockopnamesimpan');
    //Route::get('owner/stockopnamedaftar', 'stockopnamedaftar');
    //Route::get('owner/stockopnameedit/{id}', 'stockopnameedit');
    //Route::put('owner/stockopnameupdate/{id}', 'stockopnameupdate');
    //Route::get('owner/stockopnamehapus/{id}', 'stockopnamehapus');

    // laporanpembelian
    //Route::get('owner/laporanpembelian', 'laporanpembelian');
    //Route::post('owner/laporanpembeliancetak', 'laporanpembelian');
    //Route::post('owner/laporanpembelianexcel', 'laporanpembelian');

    // laporanpenjualan
    //Route::get('owner/laporanpenjualan', 'laporanpenjualan');
    //Route::post('owner/laporanpenjualan', 'laporanpenjualan');
    //Route::get('owner/laporanpenjualancetak/{tahun}/{bulan}/{namabarang}', 'laporanpenjualancetak');
    //Route::get('owner/laporanpenjualanexcel/{tahun}/{bulan}/{namabarang}', 'laporanpenjualanexcel');

    // internal
    Route::get('owner/internaltambah', 'internaltambah');
    Route::post('owner/internalsimpan', 'internalsimpan');
    Route::get('owner/internaldaftar', 'internaldaftar');
    Route::get('owner/internaledit/{id}', 'internaledit');
    Route::put('owner/internalupdate/{id}', 'internalupdate');
    Route::get('owner/internalhapus/{id}', 'internalhapus');

    // profile
    Route::get('owner/profile', 'profile');
    Route::put('owner/profileupdate', 'profileupdate');

    // Sistem PO
    Route::get('owner/po', 'poDaftar');
    Route::get('owner/po/{id}/review', 'poReview');
    Route::post('owner/po/{id}/approve', 'poApprove');
});

// -- KASIR --//
Route::middleware(['auth'])->controller(KasirController::class)->group(function () {
    Route::get('kasir', 'dashboard');

    Route::get('/kasir/dashboard', [KasirController::class, 'dashboard']);

    
    // penjualan
    Route::get('kasir/penjualantambah', [KasirController::class,'penjualantambah']);
    Route::post('kasir/penjualansimpan', [KasirController::class,'penjualansimpan']);
    Route::get('kasir/penjualandaftar', [KasirController::class,'penjualandaftar']);

    Route::get('kasir/penjualandaftar/{id}', [KasirController::class,'penjualandaftar']);
    Route::get('penjualanedit/{id}', [KasirController::class, 'penjualanedit'])->name('penjualan.edit');
    Route::put('penjualanupdate/{id}', [KasirController::class, 'penjualanupdate'])->name('penjualan.update');
    Route::get('penjualanhapus/{id}', [KasirController::class, 'penjualanhapus'])->name('penjualan.hapus');

    // Cetak Nota dan Faktur
    Route::get('kasir/cetaknota/{id}', [KasirController::class, 'cetakNota'])->name('penjualan.cetaknota');
    Route::get('kasir/cetakfaktur/{notajual}', [KasirController::class, 'cetakFaktur'])->name('penjualan.cetakfaktur');

    // Update Status Transaksi (misal: disetujui admin)
    Route::post('ubahstatustransaksi', [KasirController::class, 'ubahStatusTransaksi'])->name('barangkeluar.ubahstatus');


    //showroom
    Route::get('kasir/showroomdaftar', [KasirController::class, 'showroomdaftar']);

    

    // produk
    //Route::get('kasir/produkdaftar', 'produkdaftar');
    //Route::get('kasir/produkedit/{id}', 'produkedit');
    //Route::put('kasir/produkupdate/{id}', 'produkupdate');
    //Route::get('kasir/produkhapus/{id}', 'produkhapus');

    // barangkeluar
    ////Route::get('kasir/barangkeluartambah', 'barangkeluartambah');
    //Route::post('kasir/barangkeluarsimpan', 'barangkeluarsimpan');
    //Route::get('kasir/barangkeluardaftar', 'barangkeluardaftar');
    //Route::get('kasir/barangkeluaredit/{id}', 'barangkeluaredit');
    //Route::put('kasir/barangkeluarupdate/{id}', 'barangkeluarupdate');
    //Route::get('kasir/barangkeluarhapus/{id}', 'barangkeluarhapus');
    //Route::get('kasir/cetaknota/{id}', 'cetaknota');
    //Route::get('kasir/cetakfaktur/{id}', 'cetakfaktur');
    //Route::post('kasir/ubahstatustransaksi', 'ubahstatustransaksi');

    // profile
    Route::get('kasir/profile', 'profile');
    Route::put('kasir/profileupdate', 'profileupdate');
});


// -- GUDANG --//
Route::middleware(['auth'])->controller(GudangController::class)->group(function () {
    Route::get('gudang', 'dashboard');

    // barang produksi
    Route::get('gudang/produksidaftar', [GudangController::class, 'produksidaftar']);
    Route::get('gudang/produksitambah', [GudangController::class, 'produksitambah']);
    Route::post('gudang/produksisimpan', [GudangController::class, 'produksisimpan']);
    Route::get('gudang/produksiedit/{id}', [GudangController::class, 'produksiedit'])->name('produksi.edit');
    Route::post('gudang/produksiupdate/{id}', [GudangController::class, 'produksiupdate'])->name('produksi.update');
    Route::get('gudang/produksitambahjumlah/{id}', [GudangController::class, 'produksitambahjumlah']);
    Route::post('gudang/produksitambahjumlah/{id}', [GudangController::class, 'produksisimpanjumlah']);

    // showroom
    Route::get('gudang/showroomdaftar', [GudangController::class, 'showroomdaftar']);

    // transaksi penjualan
    Route::get('gudang/penjualandaftar', [GudangController::class, 'penjualandaftar']);
    
    // stokopname
    //Route::get('gudang/stokopnamedaftar', [GudangController::class, 'stokopnamedaftar'])->name('gudang.stokopnamedaftar'); // Halaman daftar showroom
    //Route::get('gudang/stokopnametambah/{id}', [GudangController::class, 'stokopnametambah'])->name('gudang.stokopnametambah'); // Form input stok opname
    //Route::post('gudang/stokopnamesimpan/{id}', [GudangController::class, 'stokopnamesimpan'])->name('gudang.stokopnamesimpan'); // Simpan data stok opname
    //Route::get('gudang/stokopnamedaftarhasil', [GudangController::class, 'stokopnamedaftarhasil'])->name('gudang.stokopnamedaftarhasil'); // Daftar hasil stok opname

    Route::get('gudang/stokopname', [GudangController::class, 'opnamedaftar'])->name('stokopname.daftar');
    Route::get('gudang/stokopname/tambah/{id}', [GudangController::class, 'opnametambah'])->name('stokopname.tambah');
    Route::post('gudang/stokopname/simpan', [GudangController::class, 'opnamesimpan'])->name('stokopname.simpan');
    Route::get('gudang/stokopname/riwayat', [GudangController::class, 'opnameriwayat'])->name('stokopname.riwayat');

    // laporan
    Route::get('gudang/laporan/produksi', [GudangController::class, 'laporanproduksi'])->name('laporan.gudang.produksi');
    Route::get('gudang/laporan/produksi/pdf', [GudangController::class, 'exportPdfProduksi'])->name('laporan.gudang.produksi.pdf');
    Route::get('gudang/laporan/produksi/excel', [GudangController::class, 'exportExcelProduksi'])->name('laporan.gudang.produksi.excel');

    Route::get('gudang/laporan/stokopname', [GudangController::class, 'laporanstokopname'])->name('laporan.gudang.stokopname');
    Route::get('gudang/laporan/stokopname/pdf', [GudangController::class, 'exportPdfStokOpname'])->name('laporan.gudang.stokopname.pdf');
    Route::get('gudang/laporan/stokopname/excel', [GudangController::class, 'exportExcelStokOpname'])->name('laporan.gudang.stokopname.excel');



    //Route::post('gudang/produksibatal/{id}', [GudangController::class, 'produksibatal'])->name('produksi.batal');
    

    // produk
    Route::get('gudang/produkdaftar', 'produkdaftar');

    // stockopname
    // Route::get('gudang', 'stockopnamedaftar'); // Dashboard bisa disesuaikan
    //Route::get('gudang/stockopnamedaftar', 'stockopnamedaftar');
    //Route::post('gudang/stockopnamesimpan', 'stockopnamesimpan');
    //Route::get('gudang/stockopnamehapus/{tanggalstockopname}', 'stockopnamehapus');
    //Route::get('gudang/get-produk-by-barcode/{barcode}', 'getProdukByBarcode');

    // barangkeluar
    Route::get('gudang/barangkeluartambah', 'barangkeluartambah');
    Route::post('gudang/barangkeluarsimpan', 'barangkeluarsimpan');
    Route::get('gudang/barangkeluardaftar', 'barangkeluardaftar');
    Route::get('gudang/barangkeluaredit/{id}', 'barangkeluaredit');
    Route::put('gudang/barangkeluarupdate/{id}', 'barangkeluarupdate');
    Route::get('gudang/barangkeluarhapus/{id}', 'barangkeluarhapus');
    Route::get('gudang/cetaknota/{id}', 'cetaknota');
    Route::get('gudang/cetakfaktur/{id}', 'cetakfaktur');
    Route::get('gudang/cetakstrukgudang/{id}', 'cetakstrukgudang');
    Route::get('gudang/cetakstrukcustom/{id}', 'cetakstrukcustom');
    Route::post('gudang/ubahstatustransaksi', 'ubahstatustransaksi');
    Route::post('gudang/updatecustomproduk', 'updatecustomproduk');
    Route::get('gudang/barangkeluarproses/{id}', 'barangkeluarproses');
    Route::get('gudang/barangkeluarselesai/{id}', 'barangkeluarselesai');

    // profile
    Route::get('gudang/profile', 'profile');
    Route::put('gudang/profileupdate', 'profileupdate');

    // Sistem PO 
    Route::get('gudang/po', 'poDaftar');
    Route::get('gudang/po/{id}/detail', 'poDetail');
    Route::post('gudang/po/{id}/update', 'poUpdate');
    Route::get('gudang/po/{id}/print', 'poPrint');
    Route::get('/gudang/po/{id}/print', [GudangController::class, 'poPrint'])
        ->name('gudang.po.print');
    Route::post('/gudang/po/{id}/proses', [GudangController::class, 'proses'])
        ->name('gudang.po.proses');
});
