<?php

namespace App\Http\Controllers;

use App\Models\PenjualanDetailModel;
use App\Models\ProduksiModel;
use App\Models\ShowroomModel;
use App\Models\PembelianModel;
use App\Models\PenjualanModel;
use App\Models\StokOpnameModel;
use App\Models\ProdukModel;
use App\Models\StockopnameModel;
use App\Models\User;
use App\Models\Po;
use App\Models\PoDetail;
use App\Models\HistoryHapus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OwnerController extends Controller
{
    public function dashboard()
    {
        $data['jumlahproduk'] = ProduksiModel::count();
        $data['totalstokopname'] = StokOpnameModel::whereHas('showroom.produksi')->count();
        // Total Pemasukan Penjualan Bulan Ini
        $penjualanBulanIni = PenjualanModel::whereMonth('tanggalpenjualan', now()->month)
            ->whereYear('tanggalpenjualan', now()->year)
            ->get(['bayar', 'kembali']);

        $data['totalpenjualan'] = $penjualanBulanIni->sum(function ($item) {
            $bayar = max(0, (float) ($item->bayar ?? 0));
            $kembali = max(0, (float) ($item->kembali ?? 0));

            return max(0, $bayar - $kembali);
        });
        // Total Pemasukan PO Bulan Ini
        $poBulanIni = Po::with('detail')
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->get();
            
        $data['totalpemasukanpo'] = $poBulanIni->sum(function ($po) {
            if ($po->status_pembayaran === 'DP') {
                return (int) ($po->dp ?? 0);
            }
            if ($po->status_pembayaran === 'Lunas') {
                return $po->detail->sum(function ($detail) {
                    return (int) $detail->qty * (int) $detail->harga_jual;
                });
            }

            return 0;
        });

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
            ->latest()
            ->limit(7)
            ->get();

        return view('owner.dashboard', $data);
    }
    
    public function produksidaftar()
{
    $produksi = ProduksiModel::all();
    return view('owner.produksidaftar', compact('produksi'));
}

// public function produksireview($id)
// {
//     $produksi = ProduksiModel::findOrFail($id);

//     // Hanya status "Menunggu" yang bisa direview
//     if ($produksi->status !== 'Menunggu') {
//         return redirect()->back()->with('error', 'Produksi sudah diproses atau belum siap direview.');
//     }

//     return view('owner.produksireview', compact('produksi'));
// }

public function produksireview($id)
{
    $produksi = ProduksiModel::findOrFail($id);

    return view('owner.produksireview', compact('produksi'));
}

public function produksiupdate(Request $request, $id)
{
    $produksi = ProduksiModel::findOrFail($id);
    // Hilangkan titik pemisah ribuan dari input Rupiah
    $request->merge([
        'hppfinal' => str_replace('.', '', (string) $request->input('hppfinal')),
        'hargajual' => str_replace('.', '', (string) $request->input('hargajual')),
    ]);
    
    if ($produksi->status === 'Selesai') {
        $request->validate([
            'namaproduk' => 'required|string|max:255',
            'hppfinal' => 'nullable|numeric|min:0',
            'hargajual' => 'required|numeric|min:1000',
            'deskripsiproduk' => 'nullable|string',
            'fotoproduk' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $produksi->namaproduk = $request->namaproduk;
        $produksi->hppfinal = $request->hppfinal ?? 0;
        $produksi->hargajual = $request->hargajual;
        $produksi->deskripsiproduk = $request->deskripsiproduk ?? '';

    } else {

        $request->validate([
            'namaproduk' => 'required|string|max:255',
            'stok' => 'nullable|integer|min:0',
            'stok_awal' => 'nullable|integer|min:0',
            'stok_tambahan' => 'nullable|integer|min:0',
            'hppestimasi' => 'nullable|numeric|min:0',
            'hppfinal' => 'nullable|numeric|min:0',
            'hargajual' => 'nullable|numeric|min:0',
            'tanggalproduksi' => 'nullable|date',
            'tanggalselesai' => 'nullable|date',
            'status' => 'required|in:Menunggu,Selesai',
            'deskripsiproduk' => 'nullable|string',
            'fotoproduk' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $produksi->namaproduk = $request->namaproduk;
        $produksi->stok = $request->stok ?? 0;
        $produksi->stok_awal = $request->stok_awal ?? 0;
        $produksi->stok_tambahan = $request->stok_tambahan ?? 0;
        $produksi->hppestimasi = $request->hppestimasi ?? 0;
        $produksi->hppfinal = $request->hppfinal ?? 0;
        $produksi->hargajual = $request->hargajual ?? 0;
        $produksi->tanggalproduksi = $request->tanggalproduksi;
        $produksi->status = $request->status;
        $produksi->deskripsiproduk = $request->deskripsiproduk ?? '';

        if ($request->status === 'Selesai') {
            $produksi->tanggalselesai = $request->tanggalselesai
                ? $request->tanggalselesai
                : now();
        } else {
            $produksi->tanggalselesai = $request->tanggalselesai;
        }
    }

    $fotoLama = $produksi->fotoproduk;
    $fotoBaru = null;

    try {

        if ($request->hasFile('fotoproduk')) {
            $folder = public_path('uploads/foto_produk');

            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }

            $file = $request->file('fotoproduk');
            $namaFoto = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->move($folder, $namaFoto);
            $fotoBaru = $namaFoto;
            $produksi->fotoproduk = $fotoBaru;
        }

        $produksi->tanggal_update = now();
        $produksi->save();

        if ($fotoBaru && $fotoLama) {
            $pathFotoLama = public_path('uploads/foto_produk/' . $fotoLama);

            if (file_exists($pathFotoLama)) {
                unlink($pathFotoLama);
            }
        }

        return redirect('owner/produksidaftar')
            ->with('success', 'Data produksi berhasil diperbarui.');

    } catch (\Throwable $e) {

        if ($fotoBaru) {
            $pathFotoBaru = public_path('uploads/foto_produk/' . $fotoBaru);

            if (file_exists($pathFotoBaru)) {
                unlink($pathFotoBaru);
            }
        }
        throw $e;
    }
}

public function produksireviewproses(Request $request, $id)
{
    $produksi = ProduksiModel::findOrFail($id);

    // Hanya produksi berstatus Menunggu yang bisa direview
    if ($produksi->status !== 'Menunggu') {
        return redirect('owner/produksidaftar')
            ->with('error', 'Status produksi tidak valid untuk direview.');
    }

    // Validasi HPP Final dan Harga Jual
    $request->validate([
        'hppfinal' => 'nullable|numeric|min:0',
        'hargajual' => 'required|numeric|min:1000',
    ], [
        'hppfinal.numeric' => 'HPP Final harus berupa angka.',
        'hppfinal.min' => 'HPP Final tidak boleh negatif.',
        'hargajual.required' => 'Harga Jual wajib diisi.',
        'hargajual.numeric' => 'Harga Jual harus berupa angka.',
        'hargajual.min' => 'Harga Jual minimal Rp1.000.',
    ]);

    // HPP Final kosong akan disimpan sebagai 0
    $produksi->hppfinal = $request->filled('hppfinal')
        ? $request->hppfinal
        : 0;

    $produksi->hargajual = $request->hargajual;
    $produksi->status = 'Selesai';
    $produksi->tanggalselesai = now();

    $produksi->save();

    return redirect('owner/produksidaftar')
        ->with('success', 'Review produksi berhasil disimpan.');
}

public function showroomdaftar()
{
    $data['showroom'] = ShowroomModel::whereHas('produksi')
        ->with('produksi')
        ->orderBy('tanggalmasuk', 'desc')
        ->get();

    return view('owner.showroomdaftar', $data);
}

public function showroomhapus($id)
{
    $showroom = ShowroomModel::findOrFail($id);

    HistoryHapus::create([
        'user_id' => auth()->id(),
        'nama_user' => auth()->user()->name,
        'jenis_data' => 'Showroom',
        'data_id' => $showroom->getKey(),
        'nama_data' => $showroom->nama_barang ?? 'Barang Showroom',
        'data_snapshot' => $showroom->toArray(),
        'dihapus_pada' => now(),
    ]);

    $showroom->delete();

    return redirect('owner/showroomdaftar')
        ->with('success', 'Barang showroom berhasil dihapus dan masuk ke History Hapus.');
}

public function penjualandaftar(Request $request)
{
    // Validasi pilihan filter
    $request->validate([
        'bulan' => 'nullable|integer|min:1|max:12',
        'tahun' => 'nullable|integer|min:2025|max:' . (now()->year + 5),
    ]);

    // Jika belum memilih, otomatis gunakan bulan dan tahun sekarang
    $bulan = (int) $request->input('bulan', now()->month);
    $tahun = (int) $request->input('tahun', now()->year);

    // Ambil data penjualan
    $query = PenjualanModel::with('penjualandetail');

    $query->whereMonth('tanggalpenjualan', $bulan)
          ->whereYear('tanggalpenjualan', $tahun);

    $data['penjualan'] = $query
        ->orderBy('tanggalpenjualan', 'desc')
        ->get();

    return view('owner.penjualandaftar', $data);
}

// EDIT ONGKIR PENJUALAN

public function penjualanUpdateOngkir(Request $request, $id)
{
    if (!auth()->check() || auth()->user()->role !== 'Owner') {
        abort(403, 'Anda tidak memiliki akses.');
    }

    $request->validate([
        'ongkir' => 'required|regex:/^[0-9.]+$/',
    ]);

    DB::beginTransaction();

    try {
        $penjualan = PenjualanModel::lockForUpdate()
            ->findOrFail($id);

        $ongkir = (int) str_replace('.', '', $request->ongkir);

        // Hitung total produk setelah diskon
        $totalProduk = (int) PenjualanDetailModel::where(
            'idpenjualan',
            $penjualan->idpenjualan
        )->sum('total');

        // Hitung Grand Total terbaru
        $grandtotal = $totalProduk + $ongkir;

        $dp = (int) ($penjualan->dp ?? 0);
        $bayar = (int) ($penjualan->bayar ?? 0);
        $kembali = (int) ($penjualan->kembali ?? 0);

        // Hitung total pembayaran bersih
        $totalDibayar = max(0, $dp + $bayar - $kembali);

        // Hitung sisa pembayaran
        $sisabayar = max(0, $grandtotal - $totalDibayar);

        // Tentukan status pembayaran
        $statusPembayaran = $sisabayar > 0 ? 'DP' : 'Lunas';

        $penjualan->update([
            'ongkir' => $ongkir,
            'grandtotal' => $grandtotal,
            'sisabayar' => $sisabayar,
            'statuspembayaran' => $statusPembayaran,
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Ongkir dan total pembayaran berhasil diperbarui.'
        );

    } catch (\Throwable $e) {
        DB::rollBack();

        report($e);

        return back()->with(
            'error',
            'Gagal memperbarui ongkir: ' . $e->getMessage()
        );
    }
}

// Hapus Penjualan
public function penjualanhapus($id)
{
    if (!auth()->check() || auth()->user()->role !== 'Owner') {
        abort(403, 'Anda tidak memiliki akses.');
    }

    DB::beginTransaction();

    try {
        $penjualan = PenjualanModel::with('penjualandetail')
            ->lockForUpdate()
            ->findOrFail($id);

        if ($penjualan->trashed()) {
            throw new \Exception('Transaksi sudah dihapus.');
        }

        $snapshot = [
            'penjualan' => $penjualan->toArray(),
            'detail' => $penjualan->penjualandetail->toArray(),
        ];

        // Kembalikan stok showroom
        foreach ($penjualan->penjualandetail as $detail) {

            $showroom = ShowroomModel::where(
                'idshowroom',
                $detail->idproduk
            )->lockForUpdate()->first();

            if (!$showroom) {
                throw new \Exception(
                    'Data showroom untuk produk ' .
                    $detail->namaproduk .
                    ' tidak ditemukan atau sudah dihapus.'
                );
            }

            $jumlah = (int) $detail->jumlah_pembelian;
            $stokTerjual = (int) $showroom->stok_terjual;

            if ($stokTerjual < $jumlah) {
                throw new \Exception(
                    'Stok terjual tidak mencukupi untuk mengembalikan produk ' .
                    $detail->namaproduk
                );
            }

            $showroom->stok_terjual = $stokTerjual - $jumlah;
            $showroom->stok_sisa =
                (int) $showroom->stok_awal +
                (int) $showroom->stok_tambahan -
                (int) $showroom->stok_terjual;

            $showroom->save();
        }

        HistoryHapus::create([
            'user_id' => auth()->id(),
            'nama_user' => auth()->user()->name,
            'jenis_data' => 'Penjualan',
            'data_id' => $penjualan->idpenjualan,
            'nama_data' => $penjualan->notajual,
            'data_snapshot' => $snapshot,
            'dihapus_pada' => now(),
        ]);

        $penjualan->delete();

        DB::commit();

        return back()->with(
            'success',
            'Transaksi penjualan berhasil dihapus dan stok showroom dikembalikan.'
        );

    } catch (\Throwable $e) {

        DB::rollBack();

        report($e);

        return back()->with(
            'error',
            'Gagal menghapus transaksi: ' . $e->getMessage()
        );
    }
}

public function opnamedaftar()
{
    $data = ShowroomModel::whereHas('produksi')
        ->with('produksi')
        ->orderBy('tanggalmasuk', 'desc')
        ->get();

    return view('owner.stokopnamedaftar', compact('data'));
}

public function opnameriwayat()
{
    $data = StokOpnameModel::with([
        'showroom.produksi'
    ])
    ->whereHas('showroom.produksi')
    ->orderByDesc('tanggal_opname')
    ->get();

    return view('owner.stokopnameriwayat', compact('data'));
}

//public function produksireviewlist()
//{
    //$produksi = ProduksiModel::where('status', 'Menunggu')->orderByDesc('tanggalproduksi')->get();
    //return view('owner.produksireviewlist', compact('produksi'));
//}



    public function produkdaftar()
    {
        $data['produk'] = ProdukModel::all();

        return view('owner.produkdaftar', $data);
    }


    // barang masuk
    public function barangmasukdaftar()
    {
        $data['pembelian'] = PembelianModel::groupBy('notabeli')->orderBy('tanggalpembelian', 'desc')->get();

        $pembeliandetail = [];
        foreach ($data['pembelian'] as $key => $value) {
            $pembeliandetail[$value->notabeli] = PembelianModel::where('notabeli', $value->notabeli)->get();
        }

        $data['pembeliandetail'] = $pembeliandetail;

        // return response()->Json($data);
        return view('owner.barangmasukdaftar', $data);
    }


    /*public function barangmasukhapus($id)
    {
        PembelianModel::where('notabeli', $id)->delete();

        return redirect('owner/barangmasukdaftar')->with('success', 'Data Berhasil Dihapus');
    } */

    // barangkeluar
    public function barangkeluardaftar()
    {
        $data['penjualan'] =  PenjualanModel::with(['penjualandetail'])
            ->groupBy('notajual')
            ->orderBy('tanggalpenjualan', 'desc')
            ->get();

        // return response()->Json($data);
        return view('owner.barangkeluardaftar', $data);
    }



    /* public function cetakNota($id)
    {
        $penjualan = PenjualanModel::where('notajual', $id)->get();

        if ($penjualan->isEmpty()) {
            return back()->with('error', 'Nota tidak ditemukan');
        }

        $pecah = $penjualan->first(); // Ambil data pertama
        $subtotal = $penjualan->sum('total');

        return view('owner.cetaknota', compact('penjualan', 'pecah', 'subtotal'));
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

        return view('owner.cetakfaktur', compact('penjualan', 'pecah', 'subtotal', 'grandtotal'));
    } */


    /* public function ubahstatustransaksi(Request $request)
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
    */

    // stockopname
    //public function stockopnamedaftar()
    //{
        //$stockOpnameDates = StockopnameModel::select('tanggalstockopname')
            //->distinct()
            //->orderBy('tanggalstockopname', 'DESC')
            //->paginate(6);

        //$produk = ProdukModel::withTrashed()->get();

        //$stockOpnameData = [];

        //foreach ($stockOpnameDates as $date) {
            //$tanggalStockOpname = $date->tanggalstockopname;

            //$stockOpnameData[$tanggalStockOpname] = StockopnameModel::with('produk')
                //->where('tanggalstockopname', $tanggalStockOpname)
                //->get();
        //}

        //return view('owner.stockopnamedaftar', compact('stockOpnameDates', 'produk', 'stockOpnameData'));
    //}

    /* public function stockopnamedaftar()
    {
        // Get unique stock opname dates in descending order
        $stockOpnameDates = StockopnameModel::select('tanggalstockopname')
            ->distinct()
            ->orderBy('tanggalstockopname', 'DESC')
            ->paginate(6);

        // Get products for the form dropdown
        $produk = ProdukModel::all();

        // Get all stock opname data grouped by date
        $stockOpnameData = [];

        foreach ($stockOpnameDates as $date) {
            $tanggalStockOpname = $date->tanggalstockopname;

            $stockOpnameData[$tanggalStockOpname] = StockopnameModel::with('produk')
                ->where('tanggalstockopname', $tanggalStockOpname)
                ->get();
        }

        return view('owner.stockopnamedaftar', compact('stockOpnameDates', 'produk', 'stockOpnameData'));
    }

    public function stockopnamesimpan(Request $request)
    {
        if ($request->isMethod('post')) {
            $tanggalStockOpname = $request->input('tanggalstockopname');

            $idProduk = $request->input('idproduk');
            $stokSistem = $request->input('stok');
            $stokGudang = $request->input('stokgudang');
            $selisih = $request->input('selisih');

            // Loop through all products and save stock opname data
            for ($i = 0; $i < count($idProduk); $i++) {
                if (!empty($idProduk[$i])) {
                    StockopnameModel::create([
                        'tanggalstockopname' => $tanggalStockOpname,
                        'idproduk' => $idProduk[$i],
                        'stoksistem' => $stokSistem[$i],
                        'stokgudang' => $stokGudang[$i],
                        'selisih' => $selisih[$i],
                        'waktuinputstockopname' => now()
                    ]);
                }
            }

            return redirect('owner/stockopnamedaftar')
                ->with('success', 'Stock Opname berhasil disimpan');
        }
    }

    public function stockopnamehapus($tanggalstockopname)
    {
        // Delete all stock opname entries for the given date
        StockopnameModel::where('tanggalstockopname', $tanggalstockopname)->delete();

        return redirect('owner/stockopnamedaftar')
            ->with('success', 'Data Stock Opname berhasil dihapus');
    } */

    public function getProdukByBarcode(Request $request, $barcode)
    {
        // $barcode = $request->input('barcode');

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

    public function laporanpembelian(Request $request)
    {
        $tanggalAwal = $request->input('tanggalawal', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggalakhir', now()->endOfMonth()->format('Y-m-d'));

        $pembelian = PembelianModel::whereBetween('tanggalpembelian', [$tanggalAwal, $tanggalAkhir])->get();
        $totalPengeluaran = $pembelian->sum('total');

        if ($request->cetak == 'cetak') {
            $pdf = PDF::loadView('owner.laporanpembeliancetak', compact('pembelian', 'tanggalAwal', 'tanggalAkhir', 'totalPengeluaran'));
            return $pdf->stream('Laporan_Pembelian_' . $tanggalAwal . '_sd_' . $tanggalAkhir . '.pdf');
        }

        if ($request->cetak == 'excel') {
            return $this->exportExcel($pembelian, $tanggalAwal, $tanggalAkhir, $totalPengeluaran);
        }

        return view('owner.laporanpembelian', compact('pembelian', 'tanggalAwal', 'tanggalAkhir', 'totalPengeluaran'));
    }

    public function exportExcel($pembelian, $tanggalAwal, $tanggalAkhir, $totalPengeluaran)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Pembelian');
        $sheet->setCellValue('C1', 'Nama Produk');
        $sheet->setCellValue('D1', 'Harga');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Total');

        $row = 2;
        $nomor = 1;
        foreach ($pembelian as $item) {
            $sheet->setCellValue('A' . $row, $nomor++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($item->tanggalpembelian)));
            $sheet->setCellValue('C' . $row, $item->namabarang);
            $sheet->setCellValue('D' . $row, $item->harga);
            $sheet->setCellValue('E' . $row, $item->jumlah);
            $sheet->setCellValue('F' . $row, $item->total);
            $row++;
        }

        // Total
        $sheet->setCellValue('E' . $row, 'Total Pengeluaran:');
        $sheet->setCellValue('F' . $row, $totalPengeluaran);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Pembelian_' . $tanggalAwal . '_sd_' . $tanggalAkhir . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output'); 
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function laporanpenjualan(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $namabarang = $request->input('namabarang', '');

        // Ambil daftar produk untuk dropdown
        $produk = ProdukModel::all();

        // Cek ID produk jika nama produk dipilih
        $idbarang = null;
        if ($namabarang) {
            $produkItem = ProdukModel::where('namaproduk', $namabarang)->first();
            $idbarang = $produkItem ? $produkItem->idproduk : null;
        }

        $query = PenjualanModel::query();

        // Filter tahun jika ada input tahun
        if ($tahun) {
            $query->whereYear('tanggalpenjualan', $tahun);
        }

        // Filter bulan jika ada input bulan
        if ($bulan) {
            $query->whereMonth('tanggalpenjualan', $bulan);
        }

        // Filter produk jika ada
        if ($idbarang) {
            $query->where('namabarang', $namabarang);
        }

        $penjualan = $query->orderBy('tanggalpenjualan', 'desc')->get();

        return view('owner.laporanpenjualan', compact('penjualan', 'tahun', 'bulan', 'namabarang', 'produk'));
    }


    public function laporanPenjualanCetak(Request $request, $tahun, $bulan, $namabarang)
    {
        $bulanArr = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $namabulan = $bulanArr[intval($bulan) - 1];

        $produk = ProdukModel::where('namaproduk', $namabarang)->first();
        $namaproduk = $produk ? $produk->namaproduk : "Semua Produk";

        $query = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            ->whereMonth('tanggalpenjualan', $bulan);

        if ($namabarang != 'all') {
            $query->where('namabarang', $namaproduk);
        }

        $penjualan = $query->orderBy('tanggalpenjualan', 'desc')->get();
        $totalPemasukan = $penjualan->sum('total');

        // Generate PDF menggunakan Barryvdh DomPDF
        $pdf = PDF::loadView('owner.laporanpenjualancetak', compact('penjualan', 'tahun', 'bulan', 'namabulan', 'namaproduk', 'totalPemasukan'));

        return $pdf->stream('Laporan_Penjualan_' . $tahun . '_' . $namabulan . '.pdf');
    }

    public function laporanpenjualanexcel(Request $request, $tahun, $bulan, $namabarang)
    {
        $bulanArr = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $namabulan = $bulanArr[intval($bulan) - 1];

        $query = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            ->whereMonth('tanggalpenjualan', $bulan);

        if ($namabarang != 'all') {
            $query->where('namabarang', $namabarang);
        }

        $penjualan = $query->orderBy('tanggalpenjualan', 'desc')->get();
        $totalPemasukan = $penjualan->sum('total');

        // Membuat objek spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menambahkan header ke Excel
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal Penjualan');
        $sheet->setCellValue('C1', 'Nama Produk');
        $sheet->setCellValue('D1', 'Harga');
        $sheet->setCellValue('E1', 'Jumlah');
        $sheet->setCellValue('F1', 'Total');

        $row = 2;
        $nomor = 1;
        foreach ($penjualan as $item) {
            $sheet->setCellValue('A' . $row, $nomor++);
            $sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($item->tanggalpenjualan)));
            $sheet->setCellValue('C' . $row, $item->namabarang);
            $sheet->setCellValue('D' . $row, $item->harga);
            $sheet->setCellValue('E' . $row, $item->jumlah);
            $sheet->setCellValue('F' . $row, $item->total);
            $row++;
        }

        // Menambahkan total pemasukan
        $sheet->setCellValue('E' . $row, 'Total Pemasukan:');
        $sheet->setCellValue('F' . $row, $totalPemasukan);

        // Menulis file Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Penjualan_' . $tahun . '_' . $namabulan . '.xlsx';

        // Menyiapkan response untuk download file
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        // Set header untuk mengunduh file Excel
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    // internal
    public function internaldaftar()
    {
        $users = User::whereIn('role', ['Admin', 'Kasir', 'Owner', 'Gudang'])->get();

        return view('owner.internaldaftar', compact('users'));
    }

    public function internaledit($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect('owner/internaldaftar')
                ->with('error', 'User tidak ditemukan');
        }

        return view('owner.internaledit', compact('user'));
    }

    public function internalupdate(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect('owner/internaldaftar')
                ->with('error', 'User tidak ditemukan');
        }

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|in:Owner,Admin,Gudang,Kasir',
        ]);

        $perubahan = [];

        if ($user->name != $request->name) {
            $perubahan[] = "• Nama telah diperbarui";
        }

        if ($user->email != $request->email) {
            $perubahan[] = "• Email telah diperbarui";
        }

        if ($user->role != $request->role) {
            $perubahan[] = "• Hak akses telah diperbarui";
        }

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ];

        if ($request->has('change_password') && !empty($request->password)) {
            $data['password'] = bcrypt($request->password);
            $perubahan[] = "• Password telah diperbarui";
        }

        $user->update($data);

        if (count($perubahan) == 0) {
            $perubahan[] = "Tidak ada data yang diubah.";
        }

        $info =
            "Data diperbarui oleh: " . auth()->user()->name .
            "\nWaktu: " . now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') .
            "\n\nPerubahan:\n" .
            implode("\n", $perubahan);

        return redirect('owner/internaldaftar')
            ->with('info', $info);
    }

    public function internalhapus($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect('owner/internaldaftar')
                ->with('error', 'User tidak ditemukan');
        }

        DB::beginTransaction();

        try {
            // Simpan data akun ke History Hapus
            HistoryHapus::create([
                'user_id' => auth()->id(),
                'nama_user' => auth()->user()->name,
                'jenis_data' => 'Internal',
                'data_id' => $user->id,
                'nama_data' => $user->name,
                'data_snapshot' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'alamat' => $user->alamat,
                    'nohp' => $user->nohp,
                    'role' => $user->role,
                    'file' => $user->file,
                ],
                'dihapus_pada' => now(),
            ]);

            // Soft delete akun
            $user->delete();
            DB::commit();

            return redirect('owner/internaldaftar')
                ->with('success', 'User berhasil dihapus dan masuk ke History Hapus.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect('owner/internaldaftar')
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    // profile
    public function profile()
    {
        $data['profile'] = User::find(auth()->user()->id);

        return view('owner.profile', $data);
    }

    public function profileupdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        User::where('id', auth()->user()->id)->update($data);

        return redirect('owner/profile')->with('success', 'Profile berhasil diperbarui');
    }

    // Sistem PO

    public function poDaftar()
    {
        $po = Po::with('detail')
            ->latest()
            ->get();

        return view('owner.po_daftar', compact('po'));
    }
    
    public function poReview($id)
    {
        $po = Po::with('detail')->findOrFail($id);

        // Owner hanya bisa approve jika gudang isi keterangan
        $bolehApprove = !empty($po->keterangan);

        return view('owner.detail_po', compact('po', 'bolehApprove'));
    }
  
    public function poApprove(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'Owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $po = Po::with('detail')->findOrFail($id);
        $request->validate([
            'hpp_final' => 'required|array',
            'harga_jual' => 'required|array',
            'hpp_final.*' => 'nullable',
            'harga_jual.*' => 'required',
            'status' => 'nullable|in:Approve,Reject',
        ]);

        $akanApproval = $po->status === 'Pending'
            && $request->filled('status');

        if ($akanApproval) {
            $gudangSelesai = !empty($po->keterangan);

            foreach ($po->detail as $detail) {
                if (
                    empty($detail->hpp_estimasi_gudang) ||
                    $detail->hpp_estimasi_gudang <= 0
                ) {
                    $gudangSelesai = false;
                    break;
                }
            }

            if (!$gudangSelesai) {
                return back()->with(
                    'error',
                    'PO belum bisa diapprove. Gudang harus mengisi Keterangan dan HPP Gudang terlebih dahulu.'
                );
            }
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($po->detail as $index => $detail) {
                $hppFinal = preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->hpp_final[$index] ?? '0'
                );

                $hargaJual = preg_replace(
                    '/[^0-9]/',
                    '',
                    $request->harga_jual[$index] ?? '0'
                );

                $hppFinal = $hppFinal === '' ? 0 : (int) $hppFinal;
                $hargaJual = $hargaJual === '' ? 0 : (int) $hargaJual;

                if ($hargaJual < 1000) {
                    throw new \Exception(
                        'Harga jual produk ' . $detail->produk .
                        ' minimal Rp1.000.'
                    );
                }

                $subtotal = $detail->qty * $hargaJual;
                $detail->update([
                    'hpp_final' => $hppFinal,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $status = $po->status;

            if ($akanApproval) {
                if ($request->status === 'Approve') {
                    $status = 'Disetujui';
                } elseif ($request->status === 'Reject') {
                    $status = 'Dibatalkan';
                }
            }

            $dp = (int) $po->dp;

            $sisaPembayaran = $po->status_pembayaran === 'Lunas'
                ? 0
                : max(0, $total - $dp);

            $po->update([
                'status' => $status,
                'total' => $total,
                'sisa_pembayaran' => $sisaPembayaran,
            ]);

            DB::commit();

            return redirect('owner/po')->with(
                'success',
                $akanApproval
                    ? 'PO berhasil diproses.'
                    : 'Harga PO berhasil diperbarui. Status tetap ' . $status . '.'
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan PO: ' . $e->getMessage());
        }
    }

    // DISCOUNT
    public function updateDiscount(Request $request, $id)
    {
        if (!auth()->check() || auth()->user()->role !== 'Owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $discount = str_replace(
            ',',
            '.',
            trim((string) $request->input('discount', ''))
        );

        $discountPartai = str_replace(
            ',',
            '.',
            trim((string) $request->input('discount_partai', ''))
        );

        if ($discount === '' && $discountPartai === '') {
            return back()
                ->withInput()
                ->withErrors([
                    'discount' => 'Isi minimal salah satu diskon, Non-Partai atau Partai.'
                ]);
        }

        $request->merge([
            'discount' => $discount === '' ? '0' : $discount,
            'discount_partai' => $discountPartai === '' ? '0' : $discountPartai,
        ]);

        $request->validate([
            'discount' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,4})?$/',
            ],
            'discount_partai' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,4})?$/',
            ],
        ], [
            'discount.numeric' => 'Diskon Non-Partai harus berupa angka.',
            'discount.min' => 'Diskon Non-Partai minimal 0%.',
            'discount.max' => 'Diskon Non-Partai maksimal 100%.',
            'discount.regex' => 'Diskon Non-Partai maksimal 4 angka di belakang koma.',

            'discount_partai.numeric' => 'Diskon Partai harus berupa angka.',
            'discount_partai.min' => 'Diskon Partai minimal 0%.',
            'discount_partai.max' => 'Diskon Partai maksimal 100%.',
            'discount_partai.regex' => 'Diskon Partai maksimal 4 angka di belakang koma.',
        ]);

        $item = ShowroomModel::findOrFail($id);
        $item->discount = $request->discount;
        $item->discount_partai = $request->discount_partai;
        $item->save();

        return back()->with(
            'success',
            'Diskon berhasil disimpan.'
        );
    }

    // HISTORY HAPUS

    public function historyhapus(Request $request)
    {
        if (!auth()->check() || auth()->user()->role !== 'Owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $request->validate([
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2024|max:' . date('Y'),
        ]);

        // Riwayat Hapus
        $query = HistoryHapus::whereIn('jenis_data', [
            'Produksi',
            'Showroom',
            'Internal',
            'PO',
            'Penjualan',
        ]);

        if ($request->filled('bulan')) {
            $query->whereMonth('dihapus_pada', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('dihapus_pada', $request->tahun);
        }

        $history = $query
            ->orderBy('dihapus_pada', 'desc')
            ->paginate(10);

        $history->appends($request->query());

        return view('owner.historyhapus', compact('history'));
    }

    // PULIHKAN DATA 
    public function historyhapusPulihkan($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'Owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        DB::beginTransaction();

        try {
            $history = HistoryHapus::findOrFail($id);

            if ($history->dipulihkan_pada || $history->dihapus_permanen_pada) {
                throw new \Exception('Data sudah dipulihkan atau dihapus permanen.');
            }

            if (!$history->dihapus_pada ||
                $history->dihapus_pada->lt(now()->subDays(30))) {
                throw new \Exception('Masa pemulihan data sudah melewati 30 hari.');
            }

            // Tentukan model berdasarkan jenis data
            if ($history->jenis_data === 'Produksi') {
                $data = ProduksiModel::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'Showroom') {
                $data = ShowroomModel::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'Internal') {
                $data = User::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'PO') {
                $data = Po::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'Penjualan') {
                $data = PenjualanModel::withTrashed()
                    ->findOrFail($history->data_id);

            } else {
                throw new \Exception('Jenis data belum didukung.');
            }

            if (!$data->trashed()) {
                throw new \Exception('Data sudah tidak berada di tempat sampah.');
            }

            $data->restore();

            // Jika Penjualan dipulihkan, stok showroom dikurangi kembali
            if ($history->jenis_data === 'Penjualan') {
                $detailPenjualan = PenjualanDetailModel::where(
                    'idpenjualan',
                    $data->idpenjualan
                )->get();

                foreach ($detailPenjualan as $detail) {
                    $showroom = ShowroomModel::where(
                        'idshowroom',
                        $detail->idproduk
                    )->lockForUpdate()->first();

                    if (!$showroom) {
                        throw new \Exception(
                            'Showroom untuk produk ' .
                            $detail->namaproduk .
                            'tidak ditemukan. Pemulihan dibatalkan.'
                        );
                    }

                    $jumlah = (int) $detail->jumlah_pembelian;
                    $stokTersedia =
                        (int) $showroom->stok_awal +
                        (int) $showroom->stok_tambahan -
                        (int) $showroom->stok_terjual;

                    if ($stokTersedia < $jumlah) {
                        throw new \Exception(
                            'Stok showroom tidak mencukupi untuk memulihkan transaksi.'
                        );
                    }

                    $showroom->stok_terjual =
                        (int) $showroom->stok_terjual + $jumlah;

                    $showroom->stok_sisa =
                        (int) $showroom->stok_awal +
                        (int) $showroom->stok_tambahan -
                        (int) $showroom->stok_terjual;

                    $showroom->save();
                }
            }

            // Jika jenis data adalah PO, pulihkan juga detailnya
            if ($history->jenis_data === 'PO') {

                $detailIds = collect(
                    $history->data_snapshot['detail'] ?? []
                )->pluck('id')->filter()->all();

                if (!empty($detailIds)) {
                    $details = PoDetail::withTrashed()
                        ->where('po_id', $data->id)
                        ->whereIn('id', $detailIds)
                        ->get();

                    if ($details->count() !== count($detailIds)) {
                        throw new \Exception(
                            'Sebagian detail PO tidak ditemukan. Pemulihan dibatalkan.'
                        );
                    }

                    foreach ($details as $detail) {
                        $detail->restore();
                    }
                }
            }

            $history->dipulihkan_pada = now();
            $history->save();

            DB::commit();

            return redirect()->route('owner.historyhapus')
                ->with('success', 'Data ' . $history->jenis_data . ' berhasil dipulihkan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('owner.historyhapus')
                ->with('error', 'Gagal memulihkan data: ' . $e->getMessage());
        }
    }

    // HAPUS PERMANEN DATA 

    public function historyhapusPermanen($id)
    {
        if (!auth()->check() || auth()->user()->role !== 'Owner') {
            abort(403, 'Anda tidak memiliki akses.');
        }

        $fotoPoToDelete = [];
        $fileInternalToDelete = null;

        DB::beginTransaction();

        try {
            $history = HistoryHapus::findOrFail($id);

            if ($history->dipulihkan_pada || $history->dihapus_permanen_pada) {
                throw new \Exception('Data sudah dipulihkan atau dihapus permanen.');
            }

            if ($history->jenis_data === 'Produksi') {
                $data = ProduksiModel::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'Showroom') {
                $data = ShowroomModel::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'Internal') {
                $data = User::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'Penjualan') {
                $data = PenjualanModel::withTrashed()
                    ->findOrFail($history->data_id);

            } elseif ($history->jenis_data === 'PO') {
                $data = Po::withTrashed()
                    ->findOrFail($history->data_id);

                // Ambil ID detail PO dari snapshot History Hapus
                $detailIds = collect(
                    $history->data_snapshot['detail'] ?? []
                )->pluck('id')->filter()->values()->all();

                // Cari detail PO yang sudah di-soft-delete
                $details = PoDetail::withTrashed()
                    ->where('po_id', $data->id)
                    ->whereIn('id', $detailIds)
                    ->get();

                if (count($detailIds) !== $details->count()) {
                    throw new \Exception(
                        'Sebagian detail PO tidak ditemukan. Penghapusan permanen dibatalkan.'
                    );
                }

                foreach ($details as $detail) {
                    if (!$detail->trashed()) {
                        throw new \Exception(
                            'Ada detail PO yang masih aktif. Penghapusan permanen dibatalkan.'
                        );
                    }

                    if (!empty($detail->foto)) {
                        $fotoPoToDelete[] = basename($detail->foto);
                    }
                }

                // Hapus detail PO secara permanen
                foreach ($details as $detail) {
                    $detail->forceDelete();
                }

            } else {
                throw new \Exception('Jenis data belum didukung.');
            }

            // Pastikan data utama memang sudah dihapus sementara
            if (!$data->trashed()) {
                throw new \Exception('Data sudah tidak berada di tempat sampah.');
            }

            // Simpan foto profil jika data Internal
            if ($history->jenis_data === 'Internal') {
                $fileInternalToDelete = $data->file;
            }

            // PenjualanDetailModel tidak menggunakan SoftDeletes
            if ($history->jenis_data === 'Penjualan') {
                PenjualanDetailModel::where(
                    'idpenjualan',
                    $data->idpenjualan
                )->delete();
            }

            // Hapus data utama secara permanen
            $data->forceDelete();

            // Catat waktu penghapusan permanen
            $history->dihapus_permanen_pada = now();
            $history->save();

            DB::commit();

            // Hapus foto profil Internal setelah transaksi berhasil
            if ($fileInternalToDelete) {
                Storage::disk('public')->delete($fileInternalToDelete);
            }

            // Hapus foto detail PO setelah transaksi berhasil
            foreach ($fotoPoToDelete as $foto) {
                $path = public_path('assets/foto/po/' . $foto);

                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            return redirect()->route('owner.historyhapus')
                ->with('success', 'Data ' . $history->jenis_data . ' berhasil dihapus permanen.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->route('owner.historyhapus')
                ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}