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
use App\Models\HistoryHapus;
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
        $data['menungguKonfirmasi'] = PenjualanModel::where('statuspengiriman','Menunggu Konfirmasi')->distinct('notajual')->count('notajual');
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

// Hapus Daftar Produksi

// public function produksihapus($id)
// {
//     $produksi = ProduksiModel::findOrFail($id);

//     // Hapus file foto jika ada
//     if (
//         $produksi->fotoproduk &&
//         file_exists(public_path('uploads/foto_produk/' . $produksi->fotoproduk))
//     ) {
//         unlink(public_path('uploads/foto_produk/' . $produksi->fotoproduk));
//     }

//     // Hapus data produksi
//     $produksi->delete();

//     return redirect('gudang/produksidaftar')
//         ->with('success', 'Data produksi berhasil dihapus.');
// }

public function produksihapus($id)
{
    DB::transaction(function () use ($id) {

        $produksi = ProduksiModel::findOrFail($id);

        // Simpan riwayat sebelum data dihapus
        HistoryHapus::create([
            'user_id' => Auth::id(),
            'nama_user' => Auth::user()->name,
            'jenis_data' => 'Produksi',
            'data_id' => $produksi->idproduksi,
            'nama_data' => $produksi->namaproduk,
            'data_snapshot' => $produksi->toArray(),
            'dihapus_pada' => now(),
        ]);

        // Soft delete data produksi
        $produksi->delete();
    });

    return redirect('gudang/produksidaftar')
        ->with('success', 'Data produksi berhasil dihapus.');
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
    $data['showroom'] = ShowroomModel::whereHas('produksi')
        ->with('produksi')
        ->orderBy('tanggalmasuk', 'desc')
        ->get();

    return view('gudang.showroomdaftar', $data);
}

public function penjualandaftar(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $bulan = (int) $request->input('bulan', now()->month);
    $tahun = (int) $request->input('tahun', now()->year);
    $data['penjualan'] = PenjualanModel::with('penjualandetail')
        ->whereMonth('tanggalpenjualan', $bulan)
        ->whereYear('tanggalpenjualan', $tahun)
        ->orderBy('tanggalpenjualan', 'desc')
        ->get();

    return view('gudang.penjualandaftar', $data);
}

// public function opnamedaftar()
// {
//     $data = ShowroomModel::with('produksi')->get();
//     return view('gudang.stokopnamedaftar', compact('data'));
// }

public function opnamedaftar()
{
    $data = ShowroomModel::whereHas('produksi')
        ->with('produksi')
        ->orderBy('tanggalmasuk', 'desc')
        ->get();

    return view('gudang.stokopnamedaftar', compact('data'));
}

// Form tambah opname berdasarkan showroom ID
// public function opnametambah($id)
// {
//     $showroom = ShowroomModel::with('produksi')->findOrFail($id);
//     return view('gudang.stokopnametambah', compact('showroom'));
// }

public function opnametambah($id)
{
    $showroom = ShowroomModel::whereHas('produksi')
        ->with('produksi')
        ->findOrFail($id);

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
    $data = StokOpnameModel::with('showroom.produksi')
        ->whereHas('showroom.produksi')
        ->latest()
        ->get();

    return view('gudang.stokopnameriwayat', compact('data'));
}

public function laporanproduksi(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $tahun = $request->input('tahun', now()->year);
    $query = ProduksiModel::query();

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggalproduksi', $request->bulan);
    }

    $query->whereYear('tanggalproduksi', $tahun);
    $data = $query->latest()->get();

    return view('gudang.laporanproduksi', compact('data'));
}

public function exportPdfProduksi(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $tahun = $request->input('tahun', now()->year);
    $query = ProduksiModel::query();

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggalproduksi', $request->bulan);
    }

    $query->whereYear('tanggalproduksi', $tahun);
    $data = $query->latest()->get();
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $request->filled('bulan')
        ? $namaBulan[(int) $request->bulan] . ' ' . $tahun
        : 'Januari - Desember ' . $tahun;

    $pdf = Pdf::loadView(
        'gudang.laporanproduksi_pdf',
        compact('data', 'periode')
    )->setPaper('A4', 'landscape');

    return $pdf->download(
        'Laporan-Produksi-' . $tahun .
        ($request->filled('bulan') ? '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) : '') .
        '-' . now()->format('d-m-Y') . '.pdf'
    );
}

public function exportExcelProduksi(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $tahun = $request->input('tahun', now()->year);
    $query = ProduksiModel::query();

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggalproduksi', $request->bulan);
    }

    $query->whereYear('tanggalproduksi', $tahun);
    $data = $query->latest()->get();
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $request->filled('bulan')
        ? $namaBulan[(int) $request->bulan] . ' ' . $tahun
        : 'Januari - Desember ' . $tahun;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Laporan Produksi');

    // Judul laporan
    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'LAPORAN PRODUKSI BARANG');

    $sheet->mergeCells('A2:I2');
    $sheet->setCellValue('A2', 'UD SUMBER REJEKI');

    $sheet->mergeCells('A3:I3');
    $sheet->setCellValue('A3', 'Jl. Gajah Mada No.197, Rambipuji, Jember');

    $sheet->mergeCells('A4:I4');
    $sheet->setCellValue('A4', 'Periode: ' . $periode);

    // Header tabel
    $sheet->fromArray([
        [
            'No',
            'Nama Produk',
            'Tanggal Produksi',
            'Stok Awal',
            'Stok Tambahan',
            'Stok Total',
            'HPP Final',
            'Harga Jual',
            'Status',
        ]
    ], null, 'A6');

    $row = 7;
    $nomor = 1;

    foreach ($data as $item) {

        $sheet->setCellValue('A' . $row, $nomor++);

        $sheet->setCellValue(
            'B' . $row,
            $item->namaproduk ?? '-'
        );

        $sheet->setCellValue(
            'C' . $row,
            $item->tanggalproduksi
                ? Carbon::parse($item->tanggalproduksi)->format('d-m-Y')
                : '-'
        );

        $sheet->setCellValue('D' . $row, $item->stok_awal ?? 0);
        $sheet->setCellValue('E' . $row, $item->stok_tambahan ?? 0);
        $sheet->setCellValue('F' . $row, $item->stok ?? 0);
        $sheet->setCellValue('G' . $row, $item->hppfinal ?? '-');
        $sheet->setCellValue('H' . $row, $item->hargajual ?? '-');
        $sheet->setCellValue('I' . $row, ucfirst($item->status ?? '-'));

        $row++;
    }

    $lastDataRow = $row - 1;

    if ($nomor === 1) {
        $sheet->mergeCells('A7:I7');
        $sheet->setCellValue('A7', 'Belum ada data produksi.');
        $lastDataRow = 7;
    }

    $lebar = [
        'A' => 7,
        'B' => 25,
        'C' => 18,
        'D' => 14,
        'E' => 16,
        'F' => 14,
        'G' => 17,
        'H' => 17,
        'I' => 17,
    ];

    foreach ($lebar as $kolom => $ukuran) {
        $sheet->getColumnDimension($kolom)->setWidth($ukuran);
    }

    // Format judul
    $sheet->getStyle('A1:I4')->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center');

    $sheet->getStyle('A1')->getFont()
        ->setBold(true)
        ->setSize(16);

    $sheet->getStyle('A2')->getFont()
        ->setBold(true)
        ->setSize(13);

    $sheet->getStyle('A3:I4')->getFont()
        ->setSize(10);

    $sheet->getRowDimension(1)->setRowHeight(28);
    $sheet->getRowDimension(2)->setRowHeight(23);

    // Format header tabel
    $sheet->getStyle('A6:I6')->getFont()->setBold(true);

    $sheet->getStyle('A6:I6')->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center')
        ->setWrapText(true);

    $sheet->getStyle('A6:I6')->getFill()
        ->setFillType(
            \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
        )
        ->getStartColor()->setARGB('FFD9EAF7');

    $sheet->getRowDimension(6)->setRowHeight(32);

    $sheet->getStyle('A6:I' . $lastDataRow)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(
            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        );

    if ($nomor > 1) {
        $sheet->getStyle('G7:H' . $lastDataRow)
            ->getNumberFormat()
            ->setFormatCode('"Rp" #,##0');

        // Rata tengah nomor dan tanggal
        $sheet->getStyle('A7:A' . $lastDataRow)
            ->getAlignment()->setHorizontal('center');

        $sheet->getStyle('C7:F' . $lastDataRow)
            ->getAlignment()->setHorizontal('center');

        $sheet->getStyle('I7:I' . $lastDataRow)
            ->getAlignment()->setHorizontal('center');
    }

    for ($i = 7; $i <= $lastDataRow; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(22);
    }

    $sheet->freezePane('A7');
    $sheet->getSheetView()->setZoomScale(90);

    $filename = 'Laporan-Produksi-' . $tahun .
        ($request->filled('bulan') ? '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT) : '') .
        '-' . now()->format('d-m-Y') . '.xlsx';

    return new StreamedResponse(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}

public function laporanstokopname(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $tahun = $request->input('tahun', now()->year);
    $query = StokOpnameModel::with('showroom.produksi')
        ->whereHas('showroom.produksi');

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggal_opname', $request->bulan);
    }

    $query->whereYear('tanggal_opname', $tahun);
    $data = $query->orderByDesc('tanggal_opname')->get();

    return view('gudang.laporanstokopname', compact('data'));
}

public function exportPdfStokOpname(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $tahun = $request->input('tahun', now()->year);
    $query = StokOpnameModel::with('showroom.produksi')
        ->whereHas('showroom.produksi');

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggal_opname', $request->bulan);
    }

    $query->whereYear('tanggal_opname', $tahun);
    $data = $query->orderByDesc('tanggal_opname')->get();
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $request->filled('bulan')
        ? $namaBulan[(int) $request->bulan] . ' ' . $tahun
        : 'Januari - Desember ' . $tahun;

    $pdf = Pdf::loadView(
        'gudang.laporanstokopname_pdf',
        compact('data', 'periode')
    )->setPaper('A4', 'landscape');

    return $pdf->download(
        'Laporan-Stok-Opname-' . $tahun .
        ($request->filled('bulan')
            ? '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT)
            : '') .
        '-' . now()->format('d-m-Y') . '.pdf'
    );
}

public function exportExcelStokOpname(Request $request)
{
    $request->validate([
        'bulan' => 'nullable|integer|between:1,12',
        'tahun' => 'nullable|integer|min:2025|max:2100',
    ]);

    $tahun = $request->input('tahun', now()->year);

    $query = StokOpnameModel::with('showroom.produksi')
        ->whereHas('showroom.produksi');

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggal_opname', $request->bulan);
    }

    $query->whereYear('tanggal_opname', $tahun);

    $data = $query->orderByDesc('tanggal_opname')->get();

    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $request->filled('bulan')
        ? $namaBulan[(int) $request->bulan] . ' ' . $tahun
        : 'Januari - Desember ' . $tahun;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->setTitle('Laporan Stok Opname');

    // Judul laporan
    $sheet->mergeCells('A1:G1');
    $sheet->setCellValue('A1', 'LAPORAN STOK OPNAME');

    $sheet->mergeCells('A2:G2');
    $sheet->setCellValue('A2', 'UD SUMBER REJEKI');

    $sheet->mergeCells('A3:G3');
    $sheet->setCellValue('A3', 'Jl. Gajah Mada No.197, Rambipuji, Jember');

    $sheet->mergeCells('A4:G4');
    $sheet->setCellValue('A4', 'Periode: ' . $periode);

    // Header tabel
    $sheet->fromArray([
        [
            'No',
            'Nama Produk',
            'Tanggal Opname',
            'Stok Sistem',
            'Stok Fisik',
            'Selisih',
            'Keterangan',
        ]
    ], null, 'A6');

    $row = 7;
    $nomor = 1;

    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $nomor++);

        $sheet->setCellValue(
            'B' . $row,
            $item->showroom->produksi->namaproduk ?? '-'
        );

        $sheet->setCellValue(
            'C' . $row,
            $item->tanggal_opname
                ? Carbon::parse($item->tanggal_opname)->format('d-m-Y')
                : '-'
        );

        $sheet->setCellValue('D' . $row, $item->stok_sistem ?? 0);
        $sheet->setCellValue('E' . $row, $item->stok_fisik ?? 0);
        $sheet->setCellValue('F' . $row, $item->selisih ?? 0);
        $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');

        $row++;
    }

    $lastDataRow = $row - 1;

    if ($nomor === 1) {
        $sheet->mergeCells('A7:G7');
        $sheet->setCellValue('A7', 'Belum ada data stok opname.');
        $lastDataRow = 7;
    }

    // Lebar kolom
    $lebar = [
        'A' => 7,
        'B' => 28,
        'C' => 18,
        'D' => 16,
        'E' => 16,
        'F' => 14,
        'G' => 30,
    ];

    foreach ($lebar as $kolom => $ukuran) {
        $sheet->getColumnDimension($kolom)->setWidth($ukuran);
    }

    // Format judul
    $sheet->getStyle('A1:G4')->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center');

    $sheet->getStyle('A1')->getFont()
        ->setBold(true)
        ->setSize(16);

    $sheet->getStyle('A2')->getFont()
        ->setBold(true)
        ->setSize(13);

    $sheet->getStyle('A3:G4')->getFont()
        ->setSize(10);

    $sheet->getRowDimension(1)->setRowHeight(28);
    $sheet->getRowDimension(2)->setRowHeight(23);

    // Format header tabel
    $sheet->getStyle('A6:G6')->getFont()->setBold(true);

    $sheet->getStyle('A6:G6')->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center')
        ->setWrapText(true);

    $sheet->getStyle('A6:G6')->getFill()
        ->setFillType(
            \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
        )
        ->getStartColor()->setARGB('FFD9EAF7');

    $sheet->getRowDimension(6)->setRowHeight(32);

    // Border tabel
    $sheet->getStyle('A6:G' . $lastDataRow)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(
            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        );

    if ($nomor > 1) {
        $sheet->getStyle('A7:A' . $lastDataRow)
            ->getAlignment()->setHorizontal('center');

        $sheet->getStyle('C7:F' . $lastDataRow)
            ->getAlignment()->setHorizontal('center');
    }

    for ($i = 7; $i <= $lastDataRow; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(22);
    }

    $sheet->freezePane('A7');
    $sheet->getSheetView()->setZoomScale(90);

    $filename = 'Laporan-Stok-Opname-' . $tahun .
        ($request->filled('bulan')
            ? '-' . str_pad($request->bulan, 2, '0', STR_PAD_LEFT)
            : '') .
        '-' . now()->format('d-m-Y') . '.xlsx';

    return new StreamedResponse(function () use ($spreadsheet) {
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}

// LAPORAN PENJUALAN GUDANG

public function laporanpenjualan(Request $request)
{
    $data = $this->getDataLaporanPenjualan($request);

    return view('gudang.laporanpenjualan', compact('data'));
}

private function getDataLaporanPenjualan(Request $request)
{
    $filter = $request->validate([
        'bulan' => ['nullable', 'integer', 'between:1,12'],
        'tahun' => ['nullable', 'integer', 'between:2025,2100'],
    ]);

    $bulan = (int) ($filter['bulan'] ?? now()->month);
    $tahun = (int) ($filter['tahun'] ?? now()->year);
    $dataPenjualan = PenjualanModel::with('penjualandetail')
        ->whereMonth('tanggalpenjualan', $bulan)
        ->whereYear('tanggalpenjualan', $tahun)
        ->get();

    $hasilPenjualan = [];

    foreach ($dataPenjualan as $item) {
        foreach ($item->penjualandetail as $detail) {
            $hasilPenjualan[] = [
                'tanggal' => $item->tanggalpenjualan,
                'nomor_referensi' => $item->notajual,
                'referensi' => 'Penjualan',
                'nama_produk' => $detail->namaproduk,
                'jumlah' => $detail->jumlah_pembelian,
                'harga' => $detail->harga,
                'total' => $detail->total,
                'status' => $item->statuspengiriman,
            ];
        }
    }

    $dataPo = Po::with('detail')
        ->whereMonth('tanggal', $bulan)
        ->whereYear('tanggal', $tahun)
        ->get();

    $hasilPo = [];

    foreach ($dataPo as $po) {
        foreach ($po->detail as $detail) {
            $hasilPo[] = [
                'tanggal' => $po->tanggal,
                'nomor_referensi' => $po->kode_po,
                'referensi' => 'PO',
                'nama_produk' => $detail->produk,
                'jumlah' => $detail->qty,
                'harga' => $detail->harga_jual,
                'total' => $detail->subtotal,
                'status' => $po->status,
            ];
        }
    }

    return collect(array_merge($hasilPenjualan, $hasilPo))
        ->sortByDesc('tanggal')
        ->values();
}

// EXPORT PDF LAPORAN PENJUALAN

public function exportPdfPenjualan(Request $request)
{
    $data = $this->getDataLaporanPenjualan($request);
    $bulan = (int) $request->input('bulan', now()->month);
    $tahun = (int) $request->input('tahun', now()->year);
    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $namaBulan[$bulan] . ' ' . $tahun;
    $pdf = Pdf::loadView(
        'gudang.laporanpenjualan_pdf',
        compact('data', 'periode', 'bulan', 'tahun')
    )->setPaper('A4', 'landscape');

    return $pdf->download(
        'Laporan-Penjualan-' . $tahun . '-' .
        str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' .
        now()->format('d-m-Y') . '.pdf'
    );
}

// EXPORT EXCEL LAPORAN PENJUALAN

public function exportExcelPenjualan(Request $request)
{
    $data = $this->getDataLaporanPenjualan($request);
    $bulan = (int) $request->input('bulan', now()->month);
    $tahun = (int) $request->input('tahun', now()->year);

    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $periode = $namaBulan[$bulan] . ' ' . $tahun;
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan Penjualan');

    // JUDUL LAPORAN
    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', 'LAPORAN PENJUALAN GUDANG');
    $sheet->mergeCells('A2:I2');
    $sheet->setCellValue('A2', 'UD SUMBER REJEKI');
    $sheet->mergeCells('A3:I3');
    $sheet->setCellValue('A3', 'Jl. Gajah Mada No.197, Rambipuji, Jember');
    $sheet->mergeCells('A4:I4');
    $sheet->setCellValue('A4', 'Periode: ' . $periode);

    // HEADER TABEL
    $sheet->fromArray([
        [
            'No',
            'Tanggal',
            'No Referensi',
            'Referensi',
            'Nama Produk',
            'Jumlah Terjual',
            'Harga Satuan',
            'Total Penjualan',
            'Status',
        ]
    ], null, 'A6');

    $row = 7;
    $nomor = 1;
    $totalPenjualan = 0;

    foreach ($data as $item) {
        $sheet->setCellValue('A' . $row, $nomor++);
        $sheet->setCellValue(
            'B' . $row,
            !empty($item['tanggal'])
                ? Carbon::parse($item['tanggal'])->format('d-m-Y')
                : '-'
        );

        $sheet->setCellValueExplicit(
            'C' . $row,
            (string) ($item['nomor_referensi'] ?? '-'),
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
        );

        $sheet->setCellValue('D' . $row, $item['referensi'] ?? '-');
        $sheet->setCellValue('E' . $row, $item['nama_produk'] ?? '-');
        $sheet->setCellValue('F' . $row, $item['jumlah'] ?? 0);
        $sheet->setCellValue('G' . $row, $item['harga'] ?? 0);
        $sheet->setCellValue('H' . $row, $item['total'] ?? 0);
        $sheet->setCellValue('I' . $row, $item['status'] ?? '-');
        $totalPenjualan += (float) ($item['total'] ?? 0);
        $row++;
    }

    $lastDataRow = $row - 1;

    if ($nomor === 1) {
        $sheet->mergeCells('A7:I7');
        $sheet->setCellValue('A7', 'Belum ada data penjualan.');
        $sheet->getStyle('A7:I7')
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        $lastDataRow = 7;
        $row = 8;
    }

    $lastRow = $row;
    $sheet->mergeCells('A' . $lastRow . ':G' . $lastRow);
    $sheet->setCellValue(
        'A' . $lastRow,
        'TOTAL PEMASUKAN'
    );
    $sheet->setCellValue(
        'H' . $lastRow,
        $totalPenjualan
    );
    $sheet->setCellValue('I' . $lastRow, '');

    $lebar = [
        'A' => 7,
        'B' => 17,
        'C' => 23,
        'D' => 16,
        'E' => 30,
        'F' => 17,
        'G' => 20,
        'H' => 22,
        'I' => 22,
    ];

    foreach ($lebar as $kolom => $ukuran) {
        $sheet->getColumnDimension($kolom)->setWidth($ukuran);
    }

    $sheet->getStyle('A1:I4')
        ->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center');
    $sheet->getStyle('A1')
        ->getFont()
        ->setBold(true)
        ->setSize(16);
    $sheet->getStyle('A2')
        ->getFont()
        ->setBold(true)
        ->setSize(13);
    $sheet->getStyle('A3:I4')
        ->getFont()
        ->setSize(10);
    $sheet->getRowDimension(1)->setRowHeight(30);
    $sheet->getRowDimension(2)->setRowHeight(24);
    $sheet->getRowDimension(3)->setRowHeight(20);
    $sheet->getRowDimension(4)->setRowHeight(22);

    // FORMAT HEADER TABEL
    $sheet->getStyle('A6:I6')
        ->getFont()
        ->setBold(true);
    $sheet->getStyle('A6:I6')
        ->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center')
        ->setWrapText(true);
    $sheet->getStyle('A6:I6')
        ->getFill()
        ->setFillType(
            \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
        )
        ->getStartColor()
        ->setARGB('FFD9EAF7');

    $sheet->getRowDimension(6)->setRowHeight(35);

    // FORMAT ISI TABEL
    if ($nomor > 1) {
        $sheet->getStyle('G7:H' . $lastDataRow)
            ->getNumberFormat()
            ->setFormatCode('"Rp" #,##0');
        $sheet->getStyle('A7:D' . $lastDataRow)
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');
        $sheet->getStyle('F7:F' . $lastDataRow)
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');
        $sheet->getStyle('I7:I' . $lastDataRow)
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');
        $sheet->getStyle('E7:E' . $lastDataRow)
            ->getAlignment()
            ->setHorizontal('left')
            ->setVertical('center')
            ->setWrapText(true);
        $sheet->getStyle('G7:H' . $lastDataRow)
            ->getAlignment()
            ->setHorizontal('right')
            ->setVertical('center');
    }

    $sheet->getStyle('A6:I' . $lastRow)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(
            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        );

    $sheet->getStyle('A' . $lastRow . ':I' . $lastRow)
        ->getFont()
        ->setBold(true);
    $sheet->getStyle('A' . $lastRow . ':I' . $lastRow)
        ->getFill()
        ->setFillType(
            \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID
        )
        ->getStartColor()
        ->setARGB('FFD9EAF7');
    $sheet->getStyle('A' . $lastRow . ':I' . $lastRow)
        ->getAlignment()
        ->setVertical('center');
    $sheet->getStyle('A' . $lastRow)
        ->getAlignment()
        ->setHorizontal('right');
    $sheet->getStyle('H' . $lastRow)
        ->getNumberFormat()
        ->setFormatCode('"Rp" #,##0');
    $sheet->getStyle('H' . $lastRow)
        ->getAlignment()
        ->setHorizontal('right');
    $sheet->getRowDimension($lastRow)->setRowHeight(27);

    for ($i = 7; $i <= $lastDataRow; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(25);
    }
    $sheet->freezePane('A7');
    $sheet->getSheetView()->setZoomScale(90);

    // Pengaturan halaman cetak
    $sheet->getPageSetup()
        ->setOrientation(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
        );
    $sheet->getPageSetup()
        ->setPaperSize(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
        );
    $sheet->getPageSetup()
        ->setFitToWidth(1);
    $sheet->getPageSetup()
        ->setFitToHeight(0);
    $sheet->getPageSetup()->setFitToPage(true);
    $sheet->getPageMargins()
        ->setTop(0.5)
        ->setBottom(0.5)
        ->setLeft(0.3)
        ->setRight(0.3);

    $filename = 'Laporan-Penjualan-' .
        $tahun . '-' .
        str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' .
        now()->format('d-m-Y') . '.xlsx';

    return new StreamedResponse(function () use ($spreadsheet) {

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment;filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}

// Batalkan produksi
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
        $po = Po::with('detail')->findOrFail($id);
        $hppGudang = $request->input('hpp_estimasi_gudang', []);

        foreach ($hppGudang as $detailId => $hpp) {
            $angka = preg_replace('/\D/', '', (string) $hpp);
            $hppGudang[$detailId] = $angka === '' ? null : $angka;
        }

        $request->merge([
            'hpp_estimasi_gudang' => $hppGudang
        ]);

        // Validasi input
        $request->validate([
            'estimasi_akhir' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'hpp_estimasi_gudang' => 'sometimes|array',
            'hpp_estimasi_gudang.*' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:Disetujui,Diproses,Diambil',
        ]);

        // Khusus Pending: data wajib diisi
        if ($po->status == 'Pending') {
            $request->validate([
                'estimasi_akhir' => 'required|date',
                'keterangan' => 'required|string',
                'hpp_estimasi_gudang' => 'required|array',
                'hpp_estimasi_gudang.*' => 'required|numeric|min:0',
            ], [
                'estimasi_akhir.required' => 'Estimasi akhir wajib diisi.',
                'keterangan.required' => 'Keterangan wajib diisi.',
                'hpp_estimasi_gudang.*.required' => 'HPP Estimasi Gudang wajib diisi.',
            ]);
        }

        $po->update([
            'estimasi_akhir' => $request->input('estimasi_akhir', $po->estimasi_akhir),
            'keterangan' => $request->input('keterangan', $po->keterangan),
        ]);

        // Simpan HPP Estimasi Gudang
        foreach ($hppGudang as $detailId => $hpp) {
            if ($hpp === null) {
                continue;
            }

            PoDetail::where('id', $detailId)
                ->where('po_id', $po->id)
                ->update([
                    'hpp_estimasi_gudang' => $hpp,
                ]);
        }

        if ($request->filled('status') && $request->status != $po->status) {

            // Disetujui menjadi Diproses
            if ($po->status == 'Disetujui' && $request->status == 'Diproses') {
                $po->update([
                    'status' => 'Diproses'
                ]);
            }

            // Diproses menjadi Diambil
            elseif ($po->status == 'Diproses' && $request->status == 'Diambil') {
                $po->update([
                    'status' => 'Diambil'
                ]);
            }

            else {
                return back()->with('error', 'Perubahan status PO tidak diperbolehkan.');
            }
        }

        return redirect('/gudang/po')
            ->with('success', 'Informasi Gudang berhasil diperbarui.');
    }

    public function proses($id)
    {
        $po = Po::findOrFail($id);
        if ($po->status != 'Disetujui') {
            return back()->with('error', 'PO belum bisa diproses.');
        }
        $po->update([
            'status' => 'Diproses'
        ]);

        return back()->with('success', 'PO berhasil diproses.');
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
                'gudang.po_print',
                compact('po')
            )->setPaper('A4', 'portrait');

            // return $pdf->download('PO-Internal-'.$po->kode_po.'.pdf');
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