<?php

namespace App\Http\Controllers;

use App\Models\ProduksiModel;
use App\Models\PembelianModel;
use App\Models\PenjualanModel;
use App\Models\ShowroomModel;
use App\Models\ProdukModel;
use App\Models\User;
use App\Models\Po;
use App\Models\PoDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        $data['jumlahbarangshowroom'] = ShowroomModel::count();
        $data['jumlahproduk'] = ProduksiModel::count();
        $data['totalpenjualan'] = PenjualanModel::whereMonth('tanggalpenjualan', date('m'))->sum('grandtotal');
        $data['poBaru'] = Po::whereIn('status', [
                'Pending',
                'Diproses',
                'Disetujui'
            ])
            ->latest()
            ->limit(10)
            ->get();
        
        //$bulanIni = date('m');
        //$tahunIni = date('Y');
        //$tanggalHariIni = date('Y-m-d');

        // Hitung total produk (jumlah row) di tabel ProdukModel
        //$data['jumlahproduk'] = ProdukModel::count();

        // Bulanan
        // Hitung total pembelian bulan ini
        //$data['totalpembelian'] = PembelianModel::whereMonth('tanggalpembelian', $bulanIni)
            //->whereYear('tanggalpembelian', $tahunIni)
            //->sum('grandtotal');

        // Hitung total penjualan bulan ini
        //$data['totalpenjualan'] = PenjualanModel::whereMonth('tanggalpenjualan', $bulanIni)
            //->whereYear('tanggalpenjualan', $tahunIni)
            //->sum('grandtotal');
        
        // Harian
        // $data['totalpembelianhariini'] = PembelianModel::whereDate('tanggalpembelian', $tanggalHariIni)
        //->sum('grandtotal');

        //$data['totalpenjualanhariini'] = PenjualanModel::whereDate('tanggalpenjualan', $tanggalHariIni)
        //->sum('grandtotal');

        return view('admin.dashboard', $data);
    }

    public function produksidaftar()
{
    $produksi = ProduksiModel::whereNotNull('tanggalselesai')->get();
    return view('admin.produksidaftar', compact('produksi'));
}

// Menampilkan form tambah barang showroom
public function showroomtambah()
{
    $produksi = ProduksiModel::whereNotNull('tanggalselesai')->get();

    // Ambil daftar idproduk yang sudah masuk showroom
    $produkYangSudahMasuk = ShowroomModel::pluck('idproduk')->toArray();

    return view('admin.showroomtambah', compact('produksi', 'produkYangSudahMasuk'));
}

// Menyimpan barang showroom ke database
public function showroomsimpan(Request $request)
{
    $request->validate([
        'idproduksi' => 'required|exists:produksi,idproduksi',
        'tanggalmasuk' => 'required|date',
    ]);

    $produksi = ProduksiModel::find($request->idproduksi);

    // cek apakah produk sudah pernah masuk showroom
    $cekDuplikat = ShowroomModel::where('idproduk', $produksi->idproduksi)->first();
    if ($cekDuplikat) {
        return redirect()->back()->withErrors(['Produk ini sudah pernah ditambahkan ke showroom.']);
    }

    // ✅ Validasi bahwa tanggal masuk showroom tidak boleh sebelum tanggal selesai produksi
    if (Carbon::parse($request->tanggalmasuk)->lt(Carbon::parse($produksi->tanggalselesai))) {
        return redirect()->back()->withErrors(['Tanggal masuk showroom tidak boleh sebelum tanggal selesai produksi.']);
    }

    // Generate barcode
    $barcode = date('YmdHis') . rand(100, 999);
    $link = url('scanqr/kode/' . $barcode);
    $barcodeFilename = $barcode . '.png';
    $barcodePath = public_path('assets/foto/qr/' . $barcodeFilename);

    File::ensureDirectoryExists(public_path('assets/foto/qr'));

    $generator = new BarcodeGeneratorPNG();
    file_put_contents($barcodePath, $generator->getBarcode($barcode, $generator::TYPE_CODE_128));

    // Tambahkan teks di bawah barcode
    $barcodeImage = imagecreatefrompng($barcodePath);
    $imageWidth = imagesx($barcodeImage);
    $imageHeight = imagesy($barcodeImage);
    $newHeight = $imageHeight + 30;
    $newImage = imagecreatetruecolor($imageWidth, $newHeight);

    $white = imagecolorallocate($newImage, 255, 255, 255);
    imagefilledrectangle($newImage, 0, 0, $imageWidth, $newHeight, $white);
    imagecopy($newImage, $barcodeImage, 0, 0, 0, 0, $imageWidth, $imageHeight);

    $black = imagecolorallocate($newImage, 0, 0, 0);
    $fontPath = public_path('assets/font/arial.ttf');

    if (file_exists($fontPath)) {
        $fontSize = 14;
        $textX = ($imageWidth / 2) - (strlen($barcode) * 4);
        $textY = $newHeight - 5;
        imagettftext($newImage, $fontSize, 0, $textX, $textY, $black, $fontPath, $barcode);
    } else {
        imagestring($newImage, 5, ($imageWidth / 2) - (strlen($barcode) * 3), $newHeight - 20, $barcode, $black);
    }

    imagepng($newImage, $barcodePath);
    imagedestroy($barcodeImage);
    imagedestroy($newImage);

    // Simpan data ke showroom
    ShowroomModel::create([
        'idproduk'      => $produksi->idproduksi,
        'stok_awal'     => $produksi->stok,
        'stok_tambahan' => 0,
        'stok_terjual'  => 0,
        'stok_sisa'     => $produksi->stok,
        'barcode'       => $barcode,
        'link'          => $link,
        'kodeqr'        => $barcodeFilename,
        'tanggalmasuk'  => Carbon::parse($request->tanggalmasuk),
    ]);

    return redirect('admin/showroomdaftar')->with('success', 'Barang berhasil ditambahkan ke showroom.');
}

// Menampilkan daftar barang showroom
public function showroomdaftar()
{
    $showrooms = ShowroomModel::with('produksi')->orderBy('tanggalmasuk', 'desc')->get();

    foreach ($showrooms as $item) {
        if ($item->produksi) {
            $stokProduksi = $item->produksi->stok; // stok total terbaru dari produksi
            $stokAwal = $item->stok_awal ?? 0;
            $stokTerjual = $item->stok_terjual ?? 0;
            

            //$stokTambahan = max(0, $stokProduksi - $stokAwal);
            //$stokTotal = $stokAwal + $stokTambahan;
            $stokSisa = $stokAwal - $stokTerjual;

            // Sinkronisasi ke database showroom jika data berubah
            if (
                //$item->stok_tambahan != $stokTambahan ||
                //$item->stok_total != $stokTotal ||
                $item->stok_sisa != $stokSisa
            ) {
                //$item->stok_tambahan = $stokTambahan;
                //$item->stok_total = $stokTotal;
                $item->stok_sisa = $stokSisa;
                $item->save();
    
        }
    } 
}

    return view('admin.showroomdaftar', ['showroom' => $showrooms]);
    
}

public function penjualandaftar()
{
    $data['penjualan'] = PenjualanModel::with('penjualandetail')->orderBy('tanggalpenjualan', 'desc')->get();
    return view('admin.penjualandaftar', $data);
}

public function penjualanupdate(Request $request, $id)
{
    $penjualan = PenjualanModel::findOrFail($id);

    // Hanya proses pelunasan jika ada input uang pembeli
    if ($request->has('uangpembeli')) {
        $uangPembeli = (int) str_replace(['.', ',', 'Rp', ' '], '', $request->uangpembeli);
        $dp = $penjualan->dp ?? 0;
        $grandTotal = $penjualan->grandtotal ?? 0;

        $totalBayar = $dp + $uangPembeli;
        $sisaBayar = $grandTotal - $totalBayar;
        $kembalian = $uangPembeli - ($grandTotal - $dp);

        $penjualan->bayar = $uangPembeli;
        // Jangan timpa sisabayar, biarkan nilai awal tetap ada
            if ($sisaBayar > 0) {
                $penjualan->sisabayar = $sisaBayar;
            }
        $penjualan->kembali = $kembalian > 0 ? $kembalian : 0;
        $penjualan->statuspembayaran = $sisaBayar <= 0 ? 'Lunas' : 'DP';
    }

    // Ini tetap dilakukan (update status pengiriman)
    $penjualan->statuspengiriman = $request->statuspengiriman;
    $penjualan->save();

    return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.');
}

public function Nota($notajual)
{
    $penjualan = PenjualanModel::with('penjualandetail')->where('notajual', $notajual)->firstOrFail();
    return view('admin.nota', compact('penjualan'));
}

public function Faktur($notajual)
{
    $penjualan = PenjualanModel::with('penjualandetail')->where('notajual', $notajual)->firstOrFail();

    $subtotal = $penjualan->penjualandetail->sum('total');
    $grandtotal = $penjualan->grandtotal;
    $dp = $penjualan->dp ?? 0;
    $bayarPelunasan = $penjualan->bayar ?? 0;

    // Fokus ke informasi pelunasan
    $sisaSebelumPelunasan = $grandtotal - $dp;
    $kembalian = $penjualan->kembali ?? 0;

    return view('admin.faktur', compact(
        'penjualan', 'subtotal', 'grandtotal', 'dp', 'bayarPelunasan',
        'sisaSebelumPelunasan', 'kembalian'
    ));
}

public function ubahStatusTransaksi(Request $request)
{
    $request->validate([
        'notajual' => 'required|string',
        'statustransaksi' => 'required|string|in:Dikirim,Selesai',
    ]);

    $penjualan = PenjualanModel::where('notajual', $request->notajual)->firstOrFail();
    $penjualan->statuspengiriman = $request->statustransaksi;
    $penjualan->save();

    return redirect()->back()->with('success', 'Status pengiriman berhasil diperbarui.');
}





public function produkdaftar()
    {
        $data['produk'] = ProdukModel::all();

        return view('admin.produkdaftar', $data);
    }

    public function produktambah()
    {
        $data['barangmasuk'] = PembelianModel::select(DB::raw('sum(jumlah) as jumlah'), 'namabarang')->groupBy('namabarang')->get();
        return view('admin.produktambah', $data);
    }

    public function produksimpan(Request $request)
    {
        // Validasi input
        $request->validate([
            'namaproduk' => 'required|string|max:255',
            'hargajual' => 'required|numeric',
            'foto' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        // Ambil total stok dari tabel pembelian berdasarkan nama produk
        $stok = PembelianModel::where('namabarang', $request->namaproduk)->sum('jumlah');

        $barcode = date('Ymdhis');
        $link = url('scanqr/kode/' . $barcode);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $namafoto = time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('assets/foto'), $namafoto);
        }

        // Barcode Generator
        $generator = new BarcodeGeneratorPNG();
        $barcodeFilename = $barcode . '.png';
        $barcodePath = public_path('assets/foto/qr/' . $barcodeFilename);

        File::ensureDirectoryExists(public_path('assets/foto/qr'));
        file_put_contents($barcodePath, $generator->getBarcode($barcode, $generator::TYPE_CODE_128));

        // Tambah teks ke barcode
        $barcodeImage = imagecreatefrompng($barcodePath);
        $imageWidth = imagesx($barcodeImage);
        $imageHeight = imagesy($barcodeImage);
        $newHeight = $imageHeight + 30;
        $newImage = imagecreatetruecolor($imageWidth, $newHeight);

        $white = imagecolorallocate($newImage, 255, 255, 255);
        imagefilledrectangle($newImage, 0, 0, $imageWidth, $newHeight, $white);
        imagecopy($newImage, $barcodeImage, 0, 0, 0, 0, $imageWidth, $imageHeight);

        $black = imagecolorallocate($newImage, 0, 0, 0);
        $fontPath = public_path('assets/font/arial.ttf');

        if (file_exists($fontPath)) {
            $fontSize = 14;
            $textX = ($imageWidth / 2) - (strlen($barcode) * 4);
            $textY = $newHeight - 5;
            imagettftext($newImage, $fontSize, 0, $textX, $textY, $black, $fontPath, $barcode);
        } else {
            imagestring($newImage, 5, ($imageWidth / 2) - (strlen($barcode) * 3), $newHeight - 20, $barcode, $black);
        }

        imagepng($newImage, $barcodePath);
        imagedestroy($barcodeImage);
        imagedestroy($newImage);

        // Simpan produk
        ProdukModel::create([
            'namaproduk' => $request->namaproduk,
            'stok' => $stok, // Diambil otomatis dari pembelian
            'hargajual' => $request->hargajual,
            'fotoproduk' => $namafoto,
            'barcode' => $barcode,
            'link' => $link,
            'kodeqr' => $barcodeFilename,
        ]);

        return redirect('admin/produkdaftar')->with('success', 'Produk berhasil ditambahkan!');
    }


    public function produkedit($id)
    {
        $data['produk'] = ProdukModel::find($id);

        return view('admin.produkedit', $data);
    }

    public function produkupdate(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'namaproduk' => 'required|string|max:255',
            'stok' => 'required|integer',
            'hargajual' => 'required|numeric',
        ]);

        $data = [
            'namaproduk' => $request->namaproduk,
            'stok' => $request->stok,
            'hargajual' => $request->hargajual,
        ];

        $produk = ProdukModel::find($id);
        if (!empty($request->file('foto'))) {
            if ($produk->foto && file_exists(public_path('assets/foto/' . $produk->fotoproduk))) {
                unlink(public_path('assets/foto/' . $produk->fotoproduk));
            }
            $foto = $request->file('foto');
            $namafoto = time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('assets/foto'), $namafoto);
            $data['fotoproduk'] = $namafoto;
        }

        ProdukModel::where('idproduk', $id)->update($data);

        return redirect('admin/produkdaftar')->with('success', 'Produk berhasil diperbarui!');
    }

    public function produkhapus($id)
    {
        $produk = ProdukModel::find($id);
        if ($produk->foto && file_exists(public_path('assets/foto/' . $produk->fotoproduk))) {
            unlink(public_path('assets/foto/' . $produk->fotoproduk));
        }
        ProdukModel::destroy($id);

        return redirect('admin/produkdaftar')->with('success', 'Produk berhasil dihapus!');
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
        return view('admin.barangmasukdaftar', $data);
    }

    public function barangmasuktambah()
    {
        $data['produk'] = ProdukModel::all();
        return view('admin.barangmasuktambah', $data);
    }

    public function barangmasuksimpan(Request $request)
    {
        if ($request->has('harga')) {
            $request->merge([
                'harga' => array_map(function ($harga) {
                    return str_replace('.', '', $harga);
                }, $request->input('harga')),
            ]);
        }
        if ($request->has('total')) {
            $request->merge([
                'total' => array_map(function ($total) {
                    return str_replace('.', '', $total);
                }, $request->input('total')),
            ]);
        }

        $request->validate([
            'tanggalpembelian' => 'required|date',
            'namabarang' => 'required|array',
            'namabarang.*' => 'required|string',
            'harga' => 'required|array',
            'harga.*' => 'required|numeric|min:1',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
            'total' => 'required|array',
            'total.*' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $notaBeli = date('YmdHis');

            foreach ($request->namabarang as $key => $namaBarang) {
                $harga = $request->harga[$key];
                $jumlah = $request->jumlah[$key];
                $total = $harga * $jumlah;

                if ($total != $request->total[$key]) {
                    throw new \Exception("Total tidak valid untuk item $namaBarang");
                }

                // Simpan data pembelian
                PembelianModel::create([
                    'tanggalpembelian' => $request->tanggalpembelian,
                    'notabeli' => $notaBeli,
                    'namabarang' => $namaBarang,
                    'harga' => $harga,
                    'jumlah' => $jumlah,
                    'total' => $total,
                    'grandtotal' => $request->grandtotalnon,
                ]);
            }

            // Update stok produk setelah pembelian tersimpan
            // Dapatkan semua nama produk unik dari pembelian yang baru disimpan
            $produkNamaList = array_unique($request->namabarang);

            foreach ($produkNamaList as $produkNama) {
                // Hitung total stok produk dari tabel pembelian
                $stokTerbaru = PembelianModel::where('namabarang', $produkNama)->sum('jumlah');

                // Update stok di produk model
                ProdukModel::where('namaproduk', $produkNama)->update(['stok' => $stokTerbaru]);
            }

            DB::commit();
            return redirect('admin/barangmasukdaftar')->with('success', 'Data barang masuk berhasil disimpan dan stok diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function barangmasukedit($id)
{
    // Ambil data pembelian berdasarkan notabeli
    $data['pembeliandetail'] = PembelianModel::where('notabeli', $id)->get();

    if ($data['pembeliandetail']->isEmpty()) {
        return redirect('admin/barangmasukdaftar')->with('error', 'Data tidak ditemukan');
    }

    $data['produk'] = ProdukModel::all();
    $data['notabeli'] = $id;
    $data['tanggalpembelian'] = $data['pembeliandetail']->first()->tanggalpembelian;

    return view('admin.barangmasukedit', $data);
}

public function barangmasukupdate(Request $request, $id)
{
    $request->validate([
        'tanggalpembelian' => 'required|date',
        'namabarang' => 'required|array',
        'harga' => 'required|array',
        'jumlah' => 'required|array',
        'total' => 'required|array',
    ]);

    if (!$request->tanggalpembelian || $request->tanggalpembelian === '0000-00-00') {
        return back()->with('error', 'Tanggal pembelian tidak valid atau kosong.');
    }
    


    DB::beginTransaction();
    try {
        // Hapus data lama terlebih dahulu
        PembelianModel::where('notabeli', $id)->delete();

        foreach ($request->namabarang as $key => $namaBarang) {
            $harga = str_replace('.', '', $request->harga[$key]);
            $jumlah = $request->jumlah[$key];
            $total = str_replace('.', '', $request->total[$key]);

            PembelianModel::create([
                'tanggalpembelian' => $request->tanggalpembelian,
                'notabeli' => $id,
                'namabarang' => $namaBarang,
                'harga' => $harga,
                'jumlah' => $jumlah,
                'total' => $total,
                'grandtotal' => str_replace('.', '', $request->grandtotalnon),
            ]);
        }

        // Update stok
        foreach (array_unique($request->namabarang) as $produkNama) {
            $stokTerbaru = PembelianModel::where('namabarang', $produkNama)->sum('jumlah');
            ProdukModel::where('namaproduk', $produkNama)->update(['stok' => $stokTerbaru]);
        }

       


        DB::commit();
        return redirect('admin/barangmasukdaftar')->with('success', 'Data berhasil diperbarui.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
    }
}

    public function barangmasukhapus($id)
    {
        PembelianModel::where('notabeli', $id)->delete();

        return redirect('admin/barangmasukdaftar')->with('success', 'Data Berhasil Dihapus');
    }


    // barangkeluar
    //public function barangkeluardaftar()
    //{
        //$data['penjualan'] =  PenjualanModel::with(['penjualandetail'])
            //->groupBy('notajual')
            //->orderBy('tanggalpenjualan', 'desc')
            //->get();

        // return response()->Json($data);
        //return view('admin.barangkeluardaftar', $data);
    //}

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

    public function ubahstatuspembelian(Request $request)
    {
        $notajual = $request->notajual;
        $status = 'Lunas';

        // Hilangkan titik dari input ribuan
        $uangpembeli = str_replace('.', '', $request->uangpembeli);
        $kembalian = str_replace('.', '', $request->kembalian);

        DB::table('penjualan')->where('notajual', $notajual)->update([
            'statuspembelian' => $status,
            'uangpembeli' => $uangpembeli,
            'kembalian' => $kembalian,
        ]);

        return back()->with('success', 'Status pembelian berhasil diperbarui.');
    }


    //public function barangkeluartambah()
    //{
        //$data['produk'] = ProdukModel::all();

        //return view('admin.barangkeluartambah', $data);
    //}

    //public function barangkeluarsimpan(Request $request)
    //{
        //$request->validate([
            //'tanggalpenjualan' => 'required|date',
            //'grandtotalnon' => 'required|numeric',
            //'uangpembeli' => 'nullable|numeric',
            //'kembalian' => 'required|numeric',
            //'diskon' => 'nullable|numeric',
            //'metodepembayaran' => 'required|string',
            //'namabarang' => 'required|array',
            //'namabarang.*' => 'string',
            //'harga' => 'required|array',
            //'harga.*' => 'numeric',
            //'jumlah' => 'required|array',
            //'jumlah.*' => 'integer|min:1',
            //'total' => 'required|array',
            //'total.*' => 'numeric'
        //]);

        //try {
            //DB::beginTransaction();

            //$tanggalPenjualan = $request->tanggalpenjualan;
            //$grandTotal = $request->grandtotalnon;
            //$uangPembeli = $request->uangpembeli ?? 0;
            //$kembalian = $request->kembalian;
            //$notaJual = now()->format('YmdHis');
            //$kodeNota = $this->getKodeNota($tanggalPenjualan);
            //$diskon = $request->diskon ?? 0;
            //$metodePembayaran = $request->metodepembayaran;
            //$statusTransaksi = 'Menunggu Konfirmasi Admin';

            //foreach ($request->namabarang as $i => $namaBarang) {
                //$harga = $request->harga[$i];
                //$jumlah = $request->jumlah[$i];
                //$total = $request->total[$i];

                // Simpan penjualan
                //PenjualanModel::create([
                    //'notajual' => $notaJual,
                    //'kodenota' => $kodeNota,
                    //'namabarang' => $namaBarang,
                    //'harga' => $harga,
                    //'jumlah' => $jumlah,
                    //'total' => $total,
                    //'tanggalpenjualan' => $tanggalPenjualan,
                    //'grandtotal' => $grandTotal,
                    //'uangpembeli' => $uangPembeli,
                    //'kembalian' => $kembalian,
                    //'diskon' => $diskon,
                    //'metodepembayaran' => $metodePembayaran,
                    //'statustransaksi' => $statusTransaksi
                //]);

                // Update stok produk
                //ProdukModel::where('namaproduk', $namaBarang)->decrement('stok', $jumlah);
            //}

            //DB::commit();

            //return redirect('admin/cetaknota/' . $notaJual)->with('success', 'Penjualan Barang Berhasil Disimpan');
        //} catch (\Exception $e) {
            //DB::rollback();
            //return back()->with('error', $e->getMessage());
        //}
    //}

    private function getKodeNota($tanggalPenjualan)
    {
        $tahun = date('Y', strtotime($tanggalPenjualan));
        $baseCode = $tahun . '1';

        $lastNota = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            ->orderByDesc('idpenjualan')
            ->first();

        if ($lastNota) {
            // Ambil angka setelah tahun (misal: 20241 -> ambil 1)
            // lalu tambah 1 dan gabungkan kembali
            $lastNumber = (int)substr($lastNota->kodenota, 4);
            return $tahun . ($lastNumber + 1);
        }

        return $baseCode;
    }

    

        //public function barangkeluarhapus($id)
    //{
        //PenjualanModel::where('notajual', $id)->delete();

        //return back()->with('success', 'Barang Keluar Berhasil Dihapus');
    //}

    // stockopname
    /* public function stockopnamedaftar()
    {
        // Get unique stock opname dates in descending order
        $stockOpnameDates = StockopnameModel::select('tanggalstockopname')
            ->distinct()
            ->orderBy('tanggalstockopname', 'DESC')
            ->paginate(6);

        // Get products for the form dropdown
        $produk = ProdukModel::withTrashed()->get(); // Menggunakan withTrashed() untuk mengambil produk yg dihapus juga

        // Get all stock opname data grouped by date
        $stockOpnameData = [];

        foreach ($stockOpnameDates as $date) {
            $tanggalStockOpname = $date->tanggalstockopname;

            $stockOpnameData[$tanggalStockOpname] = StockopnameModel::with('produk')
                ->where('tanggalstockopname', $tanggalStockOpname)
                ->get();
        }

        return view('admin.stockopnamedaftar', compact('stockOpnameDates', 'produk', 'stockOpnameData'));
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
                    // Periksa apakah produk ada atau telah dihapus
                    $produk = ProdukModel::withTrashed()->find($idProduk[$i]);

                    if ($produk) {
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
         }

            return redirect('admin/stockopnamedaftar')
                ->with('success', 'Stock Opname berhasil disimpan');
        }
    }

    public function stockopnamehapus($tanggalstockopname)
    {
        // Delete all stock opname entries for the given date
        StockopnameModel::where('tanggalstockopname', $tanggalstockopname)->delete();

        return redirect('admin/stockopnamedaftar')
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

    
    //public function laporanpembelian(Request $request)
    //{
        //$tanggalAwal = $request->input('tanggalawal', now()->startOfMonth()->format('Y-m-d'));
        //$tanggalAkhir = $request->input('tanggalakhir', now()->endOfMonth()->format('Y-m-d'));

        //$pembelian = PembelianModel::whereBetween('tanggalpembelian', [$tanggalAwal, $tanggalAkhir])->get();
        //$totalPengeluaran = $pembelian->sum('total');

        //if ($request->cetak == 'cetak') {
            //$pdf = PDF::loadView('admin.laporanpembeliancetak', compact('pembelian', 'tanggalAwal', 'tanggalAkhir', 'totalPengeluaran'));
            //return $pdf->stream('Laporan_Pembelian_' . $tanggalAwal . '_sd_' . $tanggalAkhir . '.pdf');
        //}

        //if ($request->cetak == 'excel') {
            //return $this->exportExcel($pembelian, $tanggalAwal, $tanggalAkhir, $totalPengeluaran);
        //}

        //return view('admin.laporanpembelian', compact('pembelian', 'tanggalAwal', 'tanggalAkhir', 'totalPengeluaran'));
    //}

    //public function exportExcel($pembelian, $tanggalAwal, $tanggalAkhir, $totalPengeluaran)
    //{
        //$spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();

        // Header
        //$sheet->setCellValue('A1', 'No');
        //$sheet->setCellValue('B1', 'Tanggal Pembelian');
        //$sheet->setCellValue('C1', 'Nama Produk');
        //$sheet->setCellValue('D1', 'Harga');
        //$sheet->setCellValue('E1', 'Jumlah');
        //$sheet->setCellValue('F1', 'Total');

        //$row = 2;
        //$nomor = 1;
        //foreach ($pembelian as $item) {
            //$sheet->setCellValue('A' . $row, $nomor++);
            //$sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($item->tanggalpembelian)));
            //$sheet->setCellValue('C' . $row, $item->namabarang);
            //$sheet->setCellValue('D' . $row, $item->harga);
            //$sheet->setCellValue('E' . $row, $item->jumlah);
            //$sheet->setCellValue('F' . $row, $item->total);
            //$row++;
        //}

        // Total
        //$sheet->setCellValue('E' . $row, 'Total Pengeluaran:');
        //$sheet->setCellValue('F' . $row, $totalPengeluaran);

        //$writer = new Xlsx($spreadsheet);
        //$fileName = 'Laporan_Pembelian_' . $tanggalAwal . '_sd_' . $tanggalAkhir . '.xlsx';

        //$response = new StreamedResponse(function () use ($writer) {
            //$writer->save('php://output');
        //});

        //$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //$response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        //$response->headers->set('Cache-Control', 'max-age=0');

        //return $response;
    //}

    //public function laporanpenjualan(Request $request)
    //{
        //$tahun = $request->input('tahun');
        //$bulan = $request->input('bulan');
        //$namabarang = $request->input('namabarang', '');

        // Ambil daftar produk untuk dropdown
        //$produk = ProdukModel::all();

        // Cek ID produk jika nama produk dipilih
        //$idbarang = null;
        //if ($namabarang) {
            //$produkItem = ProdukModel::where('namaproduk', $namabarang)->first();
            //$idbarang = $produkItem ? $produkItem->idproduk : null;
        //}

        //$query = PenjualanModel::query();

        // Filter tahun jika ada input tahun
        //if ($tahun) {
            //$query->whereYear('tanggalpenjualan', $tahun);
        //}

        // Filter bulan jika ada input bulan
        //if ($bulan) {
            //$query->whereMonth('tanggalpenjualan', $bulan);
        //}

        // Filter produk jika ada
        //if ($idbarang) {
            //$query->where('namabarang', $namabarang);
        //}

        //$penjualan = $query->orderBy('tanggalpenjualan', 'desc')->get();

        //return view('admin.laporanpenjualan', compact('penjualan', 'tahun', 'bulan', 'namabarang', 'produk'));
    //}



    //public function laporanPenjualanCetak(Request $request, $tahun, $bulan, $namabarang)
    //{
        //$bulanArr = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        //$namabulan = $bulanArr[intval($bulan) - 1];

        //$produk = ProdukModel::where('namaproduk', $namabarang)->first();
        //$namaproduk = $produk ? $produk->namaproduk : "Semua Produk";

        //$query = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            //->whereMonth('tanggalpenjualan', $bulan);

        //if ($namabarang != 'all') {
            //$query->where('namabarang', $namaproduk);
        //}

        //$penjualan = $query->orderBy('tanggalpenjualan', 'desc')->get();
        //$totalPemasukan = $penjualan->sum('total');

        // Generate PDF menggunakan Barryvdh DomPDF
        //$pdf = PDF::loadView('admin.laporanpenjualancetak', compact('penjualan', 'tahun', 'bulan', 'namabulan', 'namaproduk', 'totalPemasukan'));

        //return $pdf->stream('Laporan_Penjualan_' . $tahun . '_' . $namabulan . '.pdf');
    //}

    //public function laporanpenjualanexcel(Request $request, $tahun, $bulan, $namabarang)
    //{
        //$bulanArr = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        //$namabulan = $bulanArr[intval($bulan) - 1];

        //$query = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            //->whereMonth('tanggalpenjualan', $bulan);

        //if ($namabarang != 'all') {
            //$query->where('namabarang', $namabarang);
        //}

        //$penjualan = $query->orderBy('tanggalpenjualan', 'desc')->get();
        //$totalPemasukan = $penjualan->sum('total');

        // Membuat objek spreadsheet baru
        //$spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();

        // Menambahkan header ke Excel
        //$sheet->setCellValue('A1', 'No');
        //$sheet->setCellValue('B1', 'Tanggal Penjualan');
        //$sheet->setCellValue('C1', 'Nama Produk');
        //$sheet->setCellValue('D1', 'Harga');
        //$sheet->setCellValue('E1', 'Jumlah');
        //$sheet->setCellValue('F1', 'Total');

        //$row = 2;
        //$nomor = 1;
        //foreach ($penjualan as $item) {
            //$sheet->setCellValue('A' . $row, $nomor++);
            //$sheet->setCellValue('B' . $row, date('d-m-Y', strtotime($item->tanggalpenjualan)));
            //$sheet->setCellValue('C' . $row, $item->namabarang);
            //$sheet->setCellValue('D' . $row, $item->harga);
            //$sheet->setCellValue('E' . $row, $item->jumlah);
            //$sheet->setCellValue('F' . $row, $item->total);
            //$row++;
        //}

        // Menambahkan total pemasukan
        //$sheet->setCellValue('E' . $row, 'Total Pemasukan:');
        //$sheet->setCellValue('F' . $row, $totalPemasukan);

        // Menulis file Excel
        //$writer = new Xlsx($spreadsheet);
        //$fileName = 'Laporan_Penjualan_' . $tahun . '_' . $namabulan . '.xlsx';

        // Menyiapkan response untuk download file
        //$response = new StreamedResponse(function () use ($writer) {
            //$writer->save('php://output');
        //});

        // Set header untuk mengunduh file Excel
        //$response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //$response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        //$response->headers->set('Cache-Control', 'max-age=0');

        //return $response;
    //}


    // internal
        
    public function internaldaftar()
    {
        $users = User::whereIn('role', ['Admin', 'Kasir', 'Owner', 'Gudang'])->get();

        return view('admin.internaldaftar', compact('users'));
    }

    public function internaltambah()
    {
        return view('admin.internaltambah');
    }

    public function internalsimpan(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return redirect('admin/internaldaftar')->with('success', 'User berhasil ditambahkan');
    }

    public function internaledit($id)
    {
        $data['user'] = User::find($id);

        return view('admin.internaledit', $data);
    }

    public function internalupdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        User::where('id', $id)->update($data);

        return redirect('admin/internaldaftar')->with('success', 'User berhasil diperbarui');
    }

    public function internalhapus($id)
    {
        User::where('id', $id)->delete();

        return redirect('admin/internaldaftar')->with('success', 'User berhasil dihapus');
    }

    // profile
    public function profile()
    {
        $data['profile'] = User::find(auth()->user()->id);

        return view('admin.profile', $data);
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

        return redirect('admin/profile')->with('success', 'Profile berhasil diperbarui');
    }

    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        $path = $request->file('file')->store('profile', 'public');

        if ($user->file) {
            Storage::disk('public')->delete($user->file);
        }

        $user->file = $path;
        $user->save();

        return response()->json(['message' => 'Gambar berhasil diupdate', 'path' => $path]);
    }

    // Sistem PO 
    public function detailPo()
    {
        $po = Po::with('detail')
            ->latest()
            ->get();

        return view('admin.detail_po', compact('po'));
    }

    public function poTambah()
    {
        return view('admin.tambah_po'); 
    }

    public function poStore(Request $request)
    {
        $request->validate([
            'customer' => 'required|string',
            'produk' => 'required|array',
            'produk.*' => 'required|string',
            'deskripsi' => 'nullable|array',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:1',
            'estimasi_awal' => 'nullable|date',
            'estimasi_akhir' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {

            $po = Po::create([
                'kode_po' => 'PO-' . date('YmdHis'),
                'tanggal' => now(),
                'customer' => $request->customer,
                'estimasi_awal' => $request->estimasi_awal,
                'estimasi_akhir' => $request->estimasi_akhir,
                'status' => 'Pending',
                'total' => 0
            ]);

            $totalPO = 0;

            foreach ($request->produk as $i => $produk) {

                $qty = $request->qty[$i];
                $hppEstimasi = $request->hpp_estimasi[$i] ?? 0;
                $hargaJual = $request->harga_jual[$i] ?? 0;
                $subtotal = $hargaJual * $qty;             
                $totalPO += $subtotal;

                PoDetail::create([
                    'po_id' => $po->id,
                    'produk' => $produk,
                    'deskripsi' => $request->deskripsi[$i] ?? null,
                    'qty' => $qty,
                    'hpp_estimasi' => $hppEstimasi,
                    'harga_jual' => $hargaJual,
                    'subtotal' => $subtotal
                ]);
            }

            $po->update([
                'total' => $totalPO
            ]);

            DB::commit();

            return redirect('admin/po')
                ->with('success', 'PO berhasil dibuat');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Gagal menyimpan PO: ' . $e->getMessage()
            );
        }
    }

    public function poEdit($id)
    {
        $po = Po::with('detail')->findOrFail($id);

        if ($po->status != 'Pending') {
            return back()->with(
                'error',
                'PO hanya bisa diedit saat status Pending'
            );
        }

        return view('admin.edit_po', compact('po'));
    }

    public function poUpdate(Request $request, $id)
    {
        $po = Po::with('detail')->findOrFail($id);

        // Admin hanya bisa edit saat status Pending
        if ($po->status != 'Pending') {
            return redirect('admin/po')
                ->with('error', 'PO tidak bisa diedit karena status bukan Pending');
        }

        $request->validate([
            'customer' => 'required|string',
            'produk' => 'required|array',
            'produk.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|numeric|min:1',
            'deskripsi' => 'nullable|array',
            'estimasi_awal' => 'nullable|date',
            'estimasi_akhir' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {

            $po->update([
                'customer' => $request->customer,
                'estimasi_awal' => $request->estimasi_awal,
                'estimasi_akhir' => $request->estimasi_akhir,
            ]);

            foreach ($po->detail as $index => $detail) {

                $detail->update([
                    'produk' => $request->produk[$index],
                    'qty' => $request->qty[$index],
                    'deskripsi' => $request->deskripsi[$index] ?? null,
                ]);
            }

            DB::commit();

            return redirect('admin/po')
                ->with('success', 'PO berhasil diupdate');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                'Gagal update PO: ' . $e->getMessage()
            );
        }
    }

    public function poHapus($id)
    {
        DB::beginTransaction();

        try {

            $po = Po::findOrFail($id);

            PoDetail::where('po_id', $po->id)->delete();

            $po->delete();

            DB::commit();

            return redirect('admin/po')
                ->with('success', 'PO berhasil dihapus');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Gagal menghapus PO: ' . $e->getMessage());
        }
    }

    public function poPrint($id)
    {
        $po = Po::with('detail')->findOrFail($id);

        if (strtolower(trim($po->status)) != 'selesai') {
            return redirect('admin/po')
                ->with('error', 'PO belum bisa dicetak');
        }

        return view('admin.print_po', compact('po'));
    }
}