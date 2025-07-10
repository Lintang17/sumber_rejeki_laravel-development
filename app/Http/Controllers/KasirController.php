<?php

namespace App\Http\Controllers;

use App\Models\ShowroomModel;
//use App\Models\PembelianModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanDetailModel;
//use App\Models\ProdukModel;
use App\Models\StockopnameModel;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KasirController extends Controller
{
    public function dashboard()
    {
        //$data['jumlahproduk'] = ProdukModel::count();
        //$data['totalpembelian'] = PembelianModel::whereMonth('tanggalpembelian', date('m'))->sum('grandtotal');
        $data['totalpenjualan'] = PenjualanModel::whereMonth('tanggalpenjualan', date('m'))->sum('grandtotal');
        $data['jumlahbarangshowroom'] = ShowroomModel::count();
        $data['jumlahpenjualan'] = PenjualanModel::count();

        return view('kasir.dashboard', $data);
    }

    public function showroomdaftar()
{
    $data['showroom'] = ShowroomModel::with('produksi')->orderBy('tanggalmasuk', 'desc')->get();
    return view('kasir.showroomdaftar', $data);
}

    //public function produkdaftar()
    //{
       // $data['produk'] = ProdukModel::all();

       // return view('kasir.produkdaftar', $data);
    //}

    //public function produkedit($id)
    //{
        //$data['produk'] = ProdukModel::find($id);

        //return view('kasir.produkedit', $data);
    //}

    /* public function produkupdate(Request $request, $id)
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

        return redirect('kasir/produkdaftar')->with('success', 'Produk berhasil diperbarui!');
    }
    
    public function produkhapus($id)
    {
        $produk = ProdukModel::find($id);
        if ($produk->foto && file_exists(public_path('assets/foto/' . $produk->fotoproduk))) {
            unlink(public_path('assets/foto/' . $produk->fotoproduk));
        }
        ProdukModel::destroy($id);

        return redirect('kasir/produkdaftar')->with('success', 'Produk berhasil dihapus!');
    }
    */

    public function penjualandaftar()
{
    $data['penjualan'] = PenjualanModel::with('penjualandetail')->orderBy('tanggalpenjualan', 'desc')->get();
    return view('kasir.penjualandaftar', $data);
}

public function penjualantambah()
{
    $data['produk'] = ShowroomModel::with('produksi')
            ->whereRaw('(stok_awal + stok_tambahan - stok_terjual) > 0')
            ->get();
    return view('kasir.penjualantambah', $data);
}

public function penjualansimpan(Request $request)
{
    // Sanitasi input angka
    $stripDot = fn($val) => str_replace('.', '', $val);
    $stripArray = fn($arr) => array_map(fn($v) => str_replace('.', '', $v), $arr);

    $request->merge([
        'grandtotal' => $stripDot($request->grandtotal),
        'dp' => $stripDot($request->dp),
        'bayar' => $stripDot($request->bayar),
        'kembali' => $stripDot($request->kembali),
        'harga' => $stripArray($request->harga),
        'total' => $stripArray($request->total),
    ]);

    $request->validate([
        'tanggalpenjualan' => 'required|date',
        'namapembeli' => 'nullable|string|max:100',
        'notelp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        'metodepembayaran' => 'required|string|max:50',
        'grandtotal' => 'required|numeric',
        'dp' => 'nullable|numeric',
        'bayar' => 'nullable|numeric',
        'kembali' => 'nullable|numeric',
        'idproduk' => 'required|array',
        'jumlahpembelian' => 'required|array',
        'harga' => 'required|array',
        'total' => 'required|array',
    ]);

    DB::beginTransaction();

    try {
        $notajual = now()->format('YmdHis');
        $kodenota = $this->getKodeNota($request->tanggalpenjualan);

        $dp = (int) ($request->dp ?? 0);
        $grandtotal = (int) $request->grandtotal;
        $sisabayar = max(0, $grandtotal - $dp);

        $penjualan = PenjualanModel::create([
            'notajual' => $notajual,
            'kodenota' => $kodenota,
            'namapembeli' => $request->namapembeli,
            'notelp' => $request->notelp,
            'alamat' => $request->alamat,
            'grandtotal' => $request->grandtotal,
            'dp' => $request->dp,
            'bayar' => $request->bayar,
            'kembali' => $request->kembali,
            'tanggalpenjualan' => $request->tanggalpenjualan,
            'statuspengiriman' => 'Menunggu Konfirmasi',
            'statuspembayaran' => $request->dp > 0 ? 'DP' : 'Lunas',
            'metodepembayaran' => $request->metodepembayaran,
            'sisabayar' => $sisabayar,
        ]);

        foreach ($request->idproduk as $i => $idproduk) {
            $showroom = ShowroomModel::with('produksi')->findOrFail($idproduk);
            $jumlahPembelian = (int) $request->jumlahpembelian[$i];

            // Hitung stok sisa saat ini
            $stokTersedia = ($showroom->stok_awal + $showroom->stok_tambahan) - $showroom->stok_terjual;

            // Validasi stok
            if ($jumlahPembelian > $stokTersedia) {
                DB::rollBack();
                return back()->with('error', 'Stok tidak cukup untuk produk "' . $showroom->produksi->namaproduk. '"');
            }

            // Simpan detail penjualan
            PenjualanDetailModel::create([
                'idpenjualan' => $penjualan->idpenjualan,
                'idproduk' => $idproduk,
                'namaproduk' => $showroom->produksi->namaproduk,
                'harga' => $request->harga[$i],
                'jumlah_pembelian' => $jumlahPembelian,
                'total' => $request->total[$i],
            ]);

            // Update showroom: tambah terjual dan hitung ulang sisa
            $showroom->stok_terjual += $jumlahPembelian;
            $showroom->stok_sisa = ($showroom->stok_awal + $showroom->stok_tambahan) - $showroom->stok_terjual;
            $showroom->save();
        }

        DB::commit();

        return redirect('kasir/penjualandaftar/' . $notajual)->with('success', 'Transaksi berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
    }
}

private function getKodeNota($tanggal)
{
    $tahun = date('Y', strtotime($tanggal));
    $last = PenjualanModel::whereYear('tanggalpenjualan', $tahun)
            ->orderByDesc('idpenjualan')->first();
    return $last ? $tahun . ((int)substr($last->kodenota, 4) + 1) : $tahun . '1';
}

public function cetakNota($notajual)
{
    $penjualan = PenjualanModel::with('penjualandetail')->where('notajual', $notajual)->firstOrFail();
    $subtotal = $penjualan->penjualandetail->sum('total');
    $dp = $penjualan->dp ?? 0;

    return view('kasir.cetaknota', compact('penjualan', 'subtotal', 'dp'));
}

public function cetakFaktur($notajual)
{
    // Ambil data penjualan utama
    $pecah = PenjualanModel::where('notajual', $notajual)->firstOrFail();

    // Ambil data detail penjualan + relasi showroom
    $penjualan = PenjualanDetailModel::with('showroom')
        ->where('idpenjualan', $pecah->idpenjualan)
        ->get();

    // Hitung subtotal dan grandtotal
    $subtotal = $penjualan->sum('total');
    $grandtotal = $pecah->grandtotal;
    $dp = $pecah->dp ?? 0;

    return view('kasir.cetakfaktur', compact('pecah', 'penjualan', 'subtotal', 'grandtotal', 'dp'));
}

    //public function barangkeluarhapus($id)
    //{
        //PenjualanModel::where('notajual', $id)->delete();

        //return back()->with('success', 'Barang Keluar Berhasil Dihapus');
    //}

    // profile
    public function profile()
    {
        $data['profile'] = User::find(auth()->user()->id);

        return view('kasir.profile', $data);
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

        return redirect('kasir/profile')->with('success', 'Profile berhasil diperbarui');
    }
}
