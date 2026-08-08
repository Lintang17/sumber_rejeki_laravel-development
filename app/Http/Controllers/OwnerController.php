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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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

public function produksireview($id)
{
    $produksi = ProduksiModel::findOrFail($id);

    // Hanya status "Menunggu" yang bisa direview
    if ($produksi->status !== 'Menunggu') {
        return redirect()->back()->with('error', 'Produksi sudah diproses atau belum siap diriview.');
    }

    return view('owner.produksireview', compact('produksi'));
}

public function produksireviewproses(Request $request, $id)
{
    $produksi = ProduksiModel::findOrFail($id);

    if ($produksi->status !== 'Menunggu') {
        return redirect('owner/produksidaftar')->with('error', 'Status produksi tidak valid untuk diriview.'); 
    }

    $request->validate([
        'hppfinal' => 'required|numeric|min:0',
        'hargajual' => 'required|string|min:0',
    ]);

    $produksi->hppfinal = $request->hppfinal;
    $produksi->hargajual = $request->hargajual;
    $produksi->status = 'Selesai';
    $produksi->tanggalselesai = now();

    $produksi->save();

    return redirect('owner/produksidaftar')->with('success', 'Review produksi berhasil disimpan.');
}

public function showroomdaftar()
{
    $data['showroom'] = ShowroomModel::with('produksi')->orderBy('tanggalmasuk', 'desc')->get();
    return view('owner.showroomdaftar', $data);
}

public function penjualandaftar()
{
    $data['penjualan'] = PenjualanModel::with('penjualandetail')->orderBy('tanggalpenjualan', 'desc')->get();
    return view('owner.penjualandaftar', $data);
}

public function opnamedaftar()
{
    $data = ShowroomModel::with('produksi')->get();
    return view('owner.stokopnamedaftar', compact('data'));
}

public function opnameriwayat()
{
    $data = StokOpnameModel::with('showroom.produksi')->latest()->get();
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

        if ($user->file) {
            Storage::disk('public')->delete($user->file);
        }

        $user->delete();

        return redirect('owner/internaldaftar')
            ->with('success', 'User berhasil dihapus');
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
        $po = Po::with('detail')->findOrFail($id);

        // Gudang wajib isi keterangan dulu
        if (empty($po->keterangan)) {
            return back()->with(
                'error',
                'PO belum bisa diapprove. Gudang harus mengisi keterangan terlebih dahulu.'
            );
        }

        $request->validate([
            'hpp_final' => 'required|array',
            'harga_jual' => 'required|array',
            'status' => 'required'
        ]);

        DB::beginTransaction();

        try {

            $total = 0;

            foreach ($po->detail as $index => $detail) {

                $hppFinal = str_replace('.', '', $request->hpp_final[$index]);
                $hargaJual = str_replace('.', '', $request->harga_jual[$index]);

                // Total PO dihitung dari harga jual
                $subtotal = $detail->qty * $hargaJual;

                $detail->update([
                    'hpp_final' => $hppFinal,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;
            }

            $status = $request->status;

            // Jika approve
           if ($status == 'Approve') {
                $status = 'Disetujui';
            } elseif ($status == 'Reject') {
                $status = 'Dibatalkan';
            }

            $dp = $po->dp;
            $sisaPembayaran = 0;

            if ($po->status_pembayaran == 'DP') {
                $sisaPembayaran = max(0, $total - $dp);
            } else {
                // Jika admin memilih Lunas,
                // pembayaran dianggap penuh.
                $dp = $total;
                $sisaPembayaran = 0;
            }

            $po->update([
                'status' => $status,
                'total' => $total,
                'dp' => $dp,
                'sisa_pembayaran' => $sisaPembayaran,
            ]);

            DB::commit();

            return redirect('owner/po')
                ->with('success', 'PO berhasil diproses');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }
}
