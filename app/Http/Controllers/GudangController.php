<?php

namespace App\Http\Controllers;

use App\Models\ProduksiModel;
use App\Models\ShowroomModel;
use App\Models\PenjualanModel;
use App\Models\StokOpnameModel;
use App\Models\PembelianModel;
use App\Models\ProdukModel;
use App\Models\Po;
use App\Models\PoDetail;
//use App\Models\StockopnameModel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class GudangController extends Controller
{
    public function dashboard()
    {
        $data['jumlahproduk'] = ProduksiModel::count();
        $data['totalstokopname'] = StokOpnameModel::count();
        $data['totalpenjualan'] = PenjualanModel::whereMonth('tanggalpenjualan', date('m'))->sum('grandtotal');
        $data['poBaru'] = Po::whereIn('status', [
                'Pending',
                'Disetujui',
                'Diproses',
                'Diambil',
                'Dikirim',
                'Selesai'
            ])
            ->orderByRaw("
                CASE
                    WHEN status = 'Pending' THEN 1
                    WHEN status = 'Disetujui' THEN 2
                    WHEN status = 'Diproses' THEN 3
                    WHEN status = 'Diambil' THEN 4
                    WHEN status = 'Dikirim' THEN 5
                    WHEN status = 'Selesai' THEN 6
                    ELSE 7
                END
            ")
            ->orderByDesc('created_at')
            ->limit(7)
            ->get();

        return view('gudang.dashboard', $data);
    }

    public function produksitambah()
{
    return view('gudang.produksitambah');
}

public function produksisimpan(Request $request)
{
    $request->validate([
        'tanggalproduksi' => 'required|date',
        'namaproduk' => 'required|string|max:255',
        'stok' => 'required|integer|min:1',
        'hppestimasi' => 'required|numeric|min:0',
        'deskripsi' => 'nullable|string',
        'fotoproduk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $filename = null;

    if ($request->hasFile('fotoproduk')) {
        $file = $request->file('fotoproduk');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/foto_produk'), $filename);
    }

    ProduksiModel::create([
        'tanggalproduksi' => $request->tanggalproduksi,
        'namaproduk' => $request->namaproduk,
        'stok' => $request->stok,
        'stok_awal' => $request->stok,
        'stok_tambahan' => 0,
        'tanggal_update' => null,
        'hppestimasi' => $request->hppestimasi,
        'deskripsiproduk' => $request->deskripsi,
        'fotoproduk' => $filename,
        'status' => 'Menunggu',
        'hppfinal' => null,
        'hargajual' => null,
        'tanggalselesai' => null,
    ]);

    return redirect('gudang/produksidaftar')->with('success', 'Data produksi berhasil disimpan.');
}

public function produksidaftar()
{
    $produksi = ProduksiModel::all();
    return view('gudang.produksidaftar', compact('produksi'));
}

// ✅ Edit produksi
public function produksiedit($id)
{
    $produksi = ProduksiModel::findOrFail($id);

    // cegah edit jika sudah disetujui owner
    if ($produksi->status !== 'Menunggu') {
        return redirect('gudang/produksidaftar')->with('error', 'Produksi sudah diproses dan tidak bisa diedit.');
    }
    return view('gudang.produksiedit', compact('produksi'));
}

// ✅ Update produksi
public function produksiupdate(Request $request, $id)
{
    $produksi = ProduksiModel::findOrFail($id);

    // Hanya bisa update jika masih menunggu
    if ($produksi->status !== 'Menunggu') {
        return redirect('gudang/produksidaftar')->with('error', 'Produksi sudah diproses dan tidak bisa diubah.');
    }

    $request->validate([
        'namaproduk' => 'required|string|max:255',
        'hppestimasi' => 'required|numeric|min:0',
        'deskripsi' => 'nullable|string',
        'fotoproduk' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($request->hasFile('fotoproduk')) {
        // Hapus file lama jika ada
        if ($produksi->fotoproduk && file_exists(public_path('uploads/foto_produk/' . $produksi->fotoproduk))) {
            unlink(public_path('uploads/foto_produk/' . $produksi->fotoproduk));
        }

        $file = $request->file('fotoproduk');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/foto_produk'), $filename);
        $produksi->fotoproduk = $filename;
    }

    $produksi->namaproduk = $request->namaproduk;
    $produksi->hppestimasi = $request->hppestimasi;
    $produksi->deskripsiproduk = $request->deskripsi;

    $produksi->tanggal_update = \Carbon\Carbon::now();
    $produksi->status = 'Menunggu';
    $produksi->hppfinal = null;
    $produksi->hargajual = null;
    $produksi->tanggalselesai = null;

    $produksi->save();

    return redirect('gudang/produksidaftar')->with('success', 'Data produksi berhasil diperbarui.');
}

// Form untuk tambah jumlah produksi
public function produksitambahjumlah($id)
{
    $produksi = ProduksiModel::findOrFail($id);

    // hanya produk dengan status selesai yang bisa ditambah
    if ($produksi->status !== 'Selesai') {
        return redirect('gudang/produksidaftar')->with('error', 'Produksi belum selesai dan tidak bisa ditambah stok.');
    }

    return view('gudang.produksitambahjumlah', compact('produksi'));
}

// Menyimpan jumlah tambahan produksi
public function produksisimpanjumlah(Request $request, $id)
{
    $produksi = ProduksiModel::findOrFail($id);

    // Hanya boleh tambah jika status SELESAI
    if ($produksi->status !== 'Selesai') {
        return redirect('gudang/produksidaftar')->with('error', 'Produksi belum selesai dan tidak bisa ditambah stok.');
    }

    $request->validate([
        'stok_tambahan' => 'required|integer|min:1',
    ]);

    // ✅ Simpan jumlah awal hanya sekali
    if (is_null($produksi->stok_awal)) {
        $produksi->stok_awal = $produksi->stok;
        $produksi->stok_tambahan = 0;
    }

    // Tambahan jumlah_tambahan
    $produksi->stok_tambahan += $request->stok_tambahan;
    // Update jumlah total
    $produksi->stok = $produksi->stok_awal + $produksi->stok_tambahan;
    
    
    // Reset data hasil review Owner
    $produksi->status = 'Menunggu';
    $produksi->hppfinal = null;
    $produksi->hargajual = null;
    $produksi->tanggalselesai = null;
    $produksi->tanggal_update = now();
    
    $produksi->save();

    // ✅ Sinkronisasi stok showroom jika sudah pernah masuk showroom
$showroom = ShowroomModel::where('idproduk', $produksi->idproduksi)->first();
if ($showroom) {
    $showroom->stok_awal = $produksi->stok;
    $showroom->stok_sisa = $produksi->stok - $showroom->stok_terjual;
    $showroom->save();
}

    return redirect('gudang/produksidaftar')->with('success', 'Stok produksi berhasil ditambahkan.');
}

public function showroomdaftar()
{
    $data['showroom'] = ShowroomModel::with('produksi')->orderBy('tanggalmasuk', 'desc')->get();
    return view('gudang.showroomdaftar', $data);
}

public function penjualandaftar()
{
    $data['penjualan'] = PenjualanModel::with('penjualandetail')->orderBy('tanggalpenjualan', 'desc')->get();
    return view('gudang.penjualandaftar', $data);
}

public function opnamedaftar()
{
    $data = ShowroomModel::with('produksi')->get();
    return view('gudang.stokopnamedaftar', compact('data'));
}

// Form tambah opname berdasarkan showroom ID
public function opnametambah($id)
{
    $showroom = ShowroomModel::with('produksi')->findOrFail($id);
    return view('gudang.stokopnametambah', compact('showroom'));
}

// Simpan hasil opname
public function opnamesimpan(Request $request)
{
    $request->validate([
        'showroom_id' => 'required|exists:showroom,idshowroom',
        'tanggal_opname' => 'required|date',
        'stok_fisik' => 'required|integer|min:0',
        'keterangan' => 'nullable|string',
    ]);

    $showroom = ShowroomModel::findOrFail($request->showroom_id);
    $stok_sistem = $showroom->stok_sisa;
    $stok_fisik = $request->stok_fisik;
    $selisih = $stok_fisik - $stok_sistem;

    StokOpnameModel::create([
        'showroom_id'     => $request->showroom_id,
        'tanggal_opname'  => $request->tanggal_opname,
        'stok_sistem'     => $stok_sistem,
        'stok_fisik'      => $stok_fisik,
        'selisih'         => $selisih,
        'keterangan'      => $request->keterangan,
    ]);

    return redirect()->route('stokopname.daftar')->with('success', 'Stok opname berhasil disimpan.');
}

public function opnameriwayat()
{
    $data = StokOpnameModel::with('showroom.produksi')->latest()->get();
    return view('gudang.stokopnameriwayat', compact('data'));
}

public function laporanproduksi()
{
    $data = ProduksiModel::latest()->get();
    return view('gudang.laporanproduksi', compact('data'));
}

public function exportPdfProduksi()
{
    $data = ProduksiModel::latest()->get();

    $pdf = Pdf::loadView('gudang.laporanproduksi_pdf', compact('data'))
        ->setPaper('A4', 'landscape');

    return $pdf->download('Laporan-Produksi-' . now()->format('d-m-Y') . '.pdf');
}

public function exportExcelProduksi()
{
    $data = ProduksiModel::latest()->get();
    $tanggal = \Carbon\Carbon::now()->format('d-m-Y');

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->fromArray([
        ['No', 'Nama Produk', 'Tanggal Produksi', 'Stok Awal', 'Stok Tambahan', 'Stok Total', 'HPP Final', 'Harga Jual', 'Status']
    ], null, 'A1');

    // Data
    $row = 2;
    foreach ($data as $index => $item) {
        $sheet->setCellValue("A{$row}", $index + 1);
        $sheet->setCellValue("B{$row}", $item->namaproduk);
        $sheet->setCellValue("C{$row}", \Carbon\Carbon::parse($item->tanggalproduksi)->format('d-m-Y'));
        $sheet->setCellValue("D{$row}", $item->stok_awal);
        $sheet->setCellValue("E{$row}", $item->stok_tambahan);
        $sheet->setCellValue("F{$row}", $item->stok); // Total stok
        $sheet->setCellValue("G{$row}", $item->hppfinal ?? '-');
        $sheet->setCellValue("H{$row}", $item->hargajual ?? '-');
        $sheet->setCellValue("I{$row}", ucfirst($item->status));
        $row++;
    }

    $filename = 'Laporan-Produksi-' . $tanggal . '.xlsx';

    return new StreamedResponse(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}

public function laporanstokopname()
{
    $data = StokOpnameModel::with('showroom.produksi')->latest()->get();
    return view('gudang.laporanstokopname', compact('data'));
}

public function exportPdfStokOpname()
{
    $data = StokOpnameModel::with('showroom.produksi')->latest()->get();
    $tanggal = \Carbon\Carbon::now()->format('d-m-Y');

    $pdf = Pdf::loadView('gudang.laporanstokopname_pdf', compact('data'))
        ->setPaper('A4', 'landscape');

        return Pdf::loadView('gudang.laporanstokopname_pdf', compact('data'))
        ->download('Laporan-Stok-Opname-' . now()->format('d-m-Y') . '.pdf');
    
}

public function exportExcelStokOpname()
{
    $data = StokOpnameModel::with('showroom.produksi')->latest()->get();
    $tanggal = \Carbon\Carbon::now()->format('d-m-Y');

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Header
    $sheet->setCellValue('A1', 'No');
    $sheet->setCellValue('B1', 'Nama Produk');
    $sheet->setCellValue('C1', 'Tanggal Opname');
    $sheet->setCellValue('D1', 'Stok Sistem');
    $sheet->setCellValue('E1', 'Stok Fisik');
    $sheet->setCellValue('F1', 'Selisih');
    $sheet->setCellValue('G1', 'Keterangan');

    // Data
    $row = 2;
    foreach ($data as $index => $item) {
        $sheet->setCellValue('A' . $row, $index + 1);
        $sheet->setCellValue('B' . $row, $item->showroom->produksi->namaproduk ?? '-');
        $sheet->setCellValue('C' . $row, Carbon::parse($item->tanggal_opname)->format('d-m-Y'));
        $sheet->setCellValue('D' . $row, $item->stok_sistem);
        $sheet->setCellValue('E' . $row, $item->stok_fisik);
        $sheet->setCellValue('F' . $row, $item->selisih);
        $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
        $row++;
    }

    // Filename otomatis dengan tanggal
    $filename = 'Laporan-Stok-Opname-' . $tanggal . '.xlsx';

    // Download
    return new StreamedResponse(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}



// ✅ Batalkan produksi
//public function produksibatalkan($id)
//{
    //$produksi = ProduksiModel::findOrFail($id);

    //if (in_array($produksi->status, ['Selesai', 'Dibatalkan'])) {
        //return redirect('gudang/produksidaftar')->with('error', 'Produksi yang sudah selesai tidak bisa dibatalkan.');
    //}
        //$produksi->status = 'Dibatalkan';
        //$produksi->save();
    

    //return redirect('gudang/produksidaftar')->with('success', 'Produksi berhasil dibatalkan.');
//}

        public function produkdaftar()
        {
        $data['produk'] = ProdukModel::all();

        return view('gudang.produkdaftar', $data);
        }


    //public function stockopnamedaftar()
    //{
        //$stockOpnameDates = StockopnameModel::select('tanggalstockopname')
            //->distinct()
            //->orderBy('tanggalstockopname', 'DESC')
            //->paginate(6);

        //$produk = ProdukModel::all();

        //$stockOpnameData = [];

        //foreach ($stockOpnameDates as $date) {
            //$tanggalStockOpname = $date->tanggalstockopname;

            //$stockOpnameData[$tanggalStockOpname] = StockopnameModel::with('produk')
                //->where('tanggalstockopname', $tanggalStockOpname)
                //->get();
        //}

        //return view('gudang.stockopnamedaftar', compact('stockOpnameDates', 'produk', 'stockOpnameData'));
    //}

    //public function stockopnamesimpan(Request $request)
    //{
        //if ($request->isMethod('post')) {
            //$tanggalStockOpname = $request->input('tanggalstockopname');
            //$idProduk = $request->input('idproduk');
            //$stokSistem = $request->input('stoksistem');
            //$stokGudang = $request->input('stokgudang');
            //$selisih = $request->input('selisih');

            //for ($i = 0; $i < count($idProduk); $i++) {
                //if (!empty($idProduk[$i])) {
                    //$produk = ProdukModel::withTrashed()->find($idProduk[$i]);

                    //if ($produk) {
                        //StockopnameModel::create([
                            //'tanggalstockopname' => $tanggalStockOpname,
                            //'idproduk' => $idProduk[$i],
                            //'stoksistem' => $stokSistem[$i] ?? 0,
                            //'stokgudang' => $stokGudang[$i] ?? 0,
                            //'selisih' => $selisih[$i] ?? 0,
                            //'waktuinputstockopname' => now()
                        //]);
                    //}
                //}
            //}

            //return redirect('gudang/stockopnamedaftar')
                //->with('success', 'Stock Opname berhasil disimpan');
        //}
    //}

    //public function stockopnamehapus($tanggalstockopname)
    //{
        //StockopnameModel::where('tanggalstockopname', $tanggalstockopname)->delete();

        //return redirect('gudang/stockopnamedaftar')
           // ->with('success', 'Data Stock Opname berhasil dihapus');
   // }
    
    public function getProdukByBarcode(Request $request, $barcode)
    {
        $produk = ProdukModel::where('barcode', $barcode)->first();

        if ($produk) {
            return response()->json([
                'success' => true,
                'data' => $produk
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ]);
        }
    }

    public function profile()
    {
        return view('gudang.profile');
    }

    public function profileupdate(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect('gudang/profile')->with('success', 'Profile berhasil diupdate');
    }

    // barangkeluar
    public function barangkeluardaftar()
    {
        $data['penjualan'] =  PenjualanModel::with(['penjualandetail'])
            ->groupBy('notajual')
            ->orderBy('tanggalpenjualan', 'desc')
            ->get();

        // return response()->Json($data);
        return view('gudang.barangkeluardaftar', $data);
    }

    public function barangkeluarproses($notajual)
    {
        DB::table('penjualan')->where('notajual', $notajual)->update([
            'statusgudang' => 'Proses',
        ]);

        return back()->with('success', 'Data barang keluar berhasil diproses.');
    }

    public function barangkeluarselesai($notajual)
    {
        DB::table('penjualan')->where('notajual', $notajual)->update([
            'statusgudang' => 'Selesai',
        ]);

        return back()->with('success', 'Data barang keluar berhasil diselesaikan.');
    }

    public function updatecustomproduk(Request $request)
    {
        $idDetails = $request->idpenjualan;
        $customs = $request->custom;

        foreach ($idDetails as $index => $id) {
            DB::table('penjualan')->where('idpenjualan', $id)->update([
                'custom' => $customs[$index],
            ]);
        }

        return back()->with('success', 'Data custom produk berhasil diperbarui.');
    }

    public function barangkeluartambah()
    {
        $data['produk'] = ProdukModel::all();

        return view('gudang.barangkeluartambah', $data);
    }

    public function barangkeluarsimpan(Request $request)
    {
        $request->validate([
            'tanggalpenjualan' => 'required|date',
            'grandtotalnon' => 'required|numeric',
            'uangpembeli' => 'nullable|numeric',
            'kembalian' => 'required|numeric',
            'diskon' => 'nullable|numeric',
            'metodepembayaran' => 'required|string',
            'namabarang' => 'required|array',
            'namabarang.*' => 'string',
            'harga' => 'required|array',
            'harga.*' => 'numeric',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'total' => 'required|array',
            'total.*' => 'numeric'
        ]);

        try {
            DB::beginTransaction();

            $tanggalPenjualan = $request->tanggalpenjualan;
            $grandTotal = $request->grandtotalnon;
            $uangPembeli = $request->uangpembeli ?? 0;
            $kembalian = $request->kembalian;
            $notaJual = now()->format('YmdHis');
            $kodeNota = $this->getKodeNota($tanggalPenjualan);
            $diskon = $request->diskon ?? 0;
            $metodePembayaran = $request->metodepembayaran;
            $statusTransaksi = 'Menunggu Konfirmasi gudang';

            foreach ($request->namabarang as $i => $namaBarang) {
                $harga = $request->harga[$i];
                $jumlah = $request->jumlah[$i];
                $total = $request->total[$i];

                // Simpan penjualan
                PenjualanModel::create([
                    'notajual' => $notaJual,
                    'kodenota' => $kodeNota,
                    'namabarang' => $namaBarang,
                    'harga' => $harga,
                    'jumlah' => $jumlah,
                    'total' => $total,
                    'tanggalpenjualan' => $tanggalPenjualan,
                    'grandtotal' => $grandTotal,
                    'uangpembeli' => $uangPembeli,
                    'kembalian' => $kembalian,
                    'diskon' => $diskon,
                    'metodepembayaran' => $metodePembayaran,
                    'statustransaksi' => $statusTransaksi
                ]);

                // Update stok produk
                ProdukModel::where('namaproduk', $namaBarang)->decrement('stok', $jumlah);
            }

            DB::commit();

            return redirect('gudang/cetaknota/' . $notaJual)->with('success', 'Penjualan Barang Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    private function getKodeNota($tanggalPenjualan)
    {
        $tahun = date('Y', strtotime($tanggalPenjualan));
        $baseCode = $tahun . '1';

        $lastNota = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            ->orderByDesc('idpenjualan')
            ->first();

        if ($lastNota) {
            return $tahun . ((int)substr($lastNota->kodenota, 4) + 1); 
        }

        return $baseCode;
    }

    public function cetakstrukgudang($id)
    {
        $penjualan = DB::table('penjualan')
            ->where('notajual', $id)
            ->get();

        if ($penjualan->isEmpty()) {
            return back()->with('error', 'Nota tidak ditemukan');
        }

        $pecah = $penjualan->first();
        $subtotal = $penjualan->sum('total');

        // Ambil DP, default 0 jika tidak ada
        $dp = $pecah->dp ?? 0;

        // Hitung Grand Total: subtotal - diskon (%) - dp
        $grandtotal = $subtotal - ($subtotal * $pecah->diskon / 100) - $dp;

        // Kirim semua data yang dibutuhkan ke view
        $pdf = Pdf::loadView('gudang.cetakstrukgudang', compact(
            'penjualan',
            'pecah',
            'subtotal',
            'dp',
            'grandtotal'
        ))->setPaper('A4', 'portrait');

        return $pdf->stream("Struk_Gudang_{$id}.pdf");
    }


    public function cetakstrukcustom($id)
    {
        $penjualan = DB::table('penjualan')
            ->where('notajual', $id)
            ->get();

        if ($penjualan->isEmpty()) {
            return back()->with('error', 'Nota tidak ditemukan');
        }

        $pecah = $penjualan->first();
        $subtotal = $penjualan->sum('total');

        return view('gudang.cetakstrukcustom', compact('penjualan', 'pecah', 'subtotal'));
    }

    public function cetakNota($id)
    {
        $penjualan = PenjualanModel::where('notajual', $id)->get();

        if ($penjualan->isEmpty()) {
            return back()->with('error', 'Nota tidak ditemukan');
        }

        $pecah = $penjualan->first(); // Ambil data pertama
        $subtotal = $penjualan->sum('total');

        return view('gudang.cetaknota', compact('penjualan', 'pecah', 'subtotal'));
    }

    public function cetakfaktur($id)
    {
        $penjualan = PenjualanModel::with(['produk'])->where('notajual', $id)->get();

        // return response()->json($penjualan);

        if ($penjualan->isEmpty()) {
            return back()->with('error', 'Nota tidak ditemukan');
        }

        $pecah = $penjualan->first(); // Ambil data pertama
        $subtotal = $penjualan->sum('total');
        $grandtotal = $subtotal - ($subtotal * $pecah->diskon / 100);

        return view('gudang.cetakfaktur', compact('penjualan', 'pecah', 'subtotal', 'grandtotal'));
    }


    public function ubahstatustransaksi(Request $request)
    {
        PenjualanModel::where('notajual', $request->notajual)->update([
            'statustransaksi' => $request->statustransaksi
        ]);

        return back()->with('success', 'Status Transaksi Berhasil Diubah');
    }

    public function barangkeluarhapus($id)
    {
        PenjualanModel::where('notajual', $id)->delete();

        return back()->with('success', 'Barang Keluar Berhasil Dihapus');
    }

    // SISTEM PO
    public function poUpdate(Request $request, $id)
    {
        $request->validate([
            'estimasi_akhir' => 'nullable|date',
            'keterangan'     => 'nullable|string',
            'status'         => 'nullable|in:Diproses,Diambil',
            'hpp_estimasi_gudang.*' => 'nullable|numeric|min:0'
        ]);

        $po = Po::with('detail')->findOrFail($id);

        if (in_array($po->status, ['Diambil', 'Dikirim', 'Selesai'])) {
            return back()->with('error', 'PO sudah selesai dan tidak dapat diubah.');
        }

        if ($po->status == 'Pending') {

            if ($request->hpp_estimasi_gudang) {
                foreach ($request->hpp_estimasi_gudang as $detailId => $hpp) {
                    PoDetail::where('id', $detailId)
                        ->where('po_id', $po->id)
                        ->update([
                            'hpp_estimasi_gudang' => $hpp
                        ]);
                }
            }

            $po->update([
                'estimasi_akhir' => $request->estimasi_akhir,
                'keterangan'     => $request->keterangan,
            ]);

            return back()->with('success', 'Data gudang berhasil disimpan.');
        }

        if ($po->status == 'Disetujui') {
    
            if ($request->status != 'Diproses') {
                return back()->with('error', 'PO harus diubah ke Diproses.');
            }

            $po->update([
                'status'         => 'Diproses',
                'estimasi_akhir' => $request->estimasi_akhir,
                'keterangan'     => $request->keterangan,
            ]);

            return back()->with('success', 'PO berhasil diproses (Print Internal siap).');
        }

        if ($po->status == 'Diproses') {

            if ($request->status != 'Diambil') {
                return back()->with('error', 'PO hanya bisa diubah ke Diambil.');
            }

            $po->update([
                'status'         => 'Diambil',
                'estimasi_akhir' => $request->estimasi_akhir,
                'keterangan'     => $request->keterangan,
            ]);

            return back()->with('success', 'PO sudah diambil (Surat pengambilan siap).');
        }

        return back()->with('error', 'Status tidak valid.');
    }

    public function poDetail($id)
    {
        $po = Po::with('detail')->findOrFail($id);

        return view('gudang.detail_po', compact('po'));
    }

    public function poPrint($id)
    {
        $po = Po::with('detail')->findOrFail($id);

        // hanya boleh print saat Diproses atau Diambil
        if (!in_array($po->status, ['Diproses', 'Diambil'])) {
            return back()->with('error', 'PO belum dapat dicetak.');
        }

        // PRINT INTERNAL GUDANG
        if ($po->status == 'Diproses') {

            $pdf = Pdf::loadView(
                'gudang.po_print_internal',
                compact('po')
            )->setPaper('A4', 'portrait');

            return $pdf->stream('PO-Internal-'.$po->kode_po.'.pdf');
        }

        // SURAT PENGAMBILAN
        $pdf = Pdf::loadView(
            'gudang.po_print_pengambilan',
            compact('po')
        )->setPaper('A4', 'portrait');

        return $pdf->stream('Surat-Pengambilan-'.$po->kode_po.'.pdf');
    }

    public function poDaftar()
    {
      $po = Po::with('detail')
        ->whereIn('status', [
            'Pending',
            'Disetujui',
            'Diproses',
            'Diambil',
            'Dikirim',
            'Selesai'
        ])
        ->latest()
        ->get();

        return view('gudang.po_daftar', compact('po'));
    }
}