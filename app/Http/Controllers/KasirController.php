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
        // Notifikasi transaksi penjualan terbaru
        $data['penjualanTerbaru'] = PenjualanModel::orderBy('tanggalpenjualan', 'desc')
            ->take(5)
            ->get();

        return view('kasir.dashboard', $data);
    }

    public function showroomdaftar()
    {
        $data['showroom'] = ShowroomModel::whereHas('produksi')
            ->with('produksi')
            ->orderBy('tanggalmasuk', 'desc')
            ->get();

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

public function penjualandaftar(Request $request)
{
    // $data['penjualan'] = PenjualanModel::with('penjualandetail')->orderBy('tanggalpenjualan', 'desc')->get();
    // return view('kasir.penjualandaftar', $data);

    $query = PenjualanModel::with('penjualandetail.showroom');

    if ($request->filled('bulan')) {
        $query->whereMonth('tanggalpenjualan', $request->bulan);
    }

    if ($request->filled('tahun')) {
        $query->whereYear('tanggalpenjualan', $request->tahun);
    }

    $data['penjualan'] = $query
        ->orderBy('tanggalpenjualan', 'desc')
        ->get();

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
    $request->validate([
        'tanggalpenjualan' => 'required|date',
        'namapembeli' => 'nullable|string|max:100',
        'notelp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        'metodepembayaran' => 'required|string|max:50',
        'dp' => 'nullable|string',
        'bayar' => 'nullable|string',
        'idproduk' => 'required|array|min:1',
        'idproduk.*' => 'required|integer',
        'jumlahpembelian' => 'required|array|min:1',
        'jumlahpembelian.*' => 'required|integer|min:1',
        'jenis_diskon' => 'required|array|min:1',
        'jenis_diskon.*' => 'required|in:non_partai,partai',
    ]);

    if (
        count($request->idproduk) !== count($request->jumlahpembelian) ||
        count($request->idproduk) !== count($request->jenis_diskon)
    ) {
        return back()->withInput()->with(
            'error',
            'Data produk, jumlah, dan jenis diskon tidak sesuai.'
        );
    }

    // Ubah format rupiah menjadi angka.
    $angka = function ($nilai) {
        return (int) str_replace('.', '', $nilai ?? '0');
    };

    DB::beginTransaction();

    try {
        $notajual = now()->format('YmdHis');
        $kodenota = $this->getKodeNota($request->tanggalpenjualan);

        $dp = $angka($request->dp);
        $bayar = $angka($request->bayar);
        $grandtotal = 0;

        $penjualan = PenjualanModel::create([
            'notajual' => $notajual,
            'kodenota' => $kodenota,
            'namapembeli' => $request->namapembeli,
            'notelp' => $request->notelp,
            'alamat' => $request->alamat,
            'grandtotal' => 0,
            'dp' => $dp,
            'bayar' => $bayar,
            'kembali' => 0,
            'tanggalpenjualan' => $request->tanggalpenjualan,
            'statuspengiriman' => 'Menunggu Konfirmasi',
            'statuspembayaran' => $dp > 0 ? 'DP' : 'Lunas',
            'metodepembayaran' => $request->metodepembayaran,
            'sisabayar' => 0,
        ]);

        foreach ($request->idproduk as $i => $idproduk) {
            $showroom = ShowroomModel::with('produksi')
                ->where('idshowroom', $idproduk)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$showroom->produksi) {
                throw new \Exception('Data produk tidak ditemukan.');
            }

            $jumlah = (int) $request->jumlahpembelian[$i];

            $stok = (int) $showroom->stok_awal
                + (int) $showroom->stok_tambahan
                - (int) $showroom->stok_terjual;

            if ($jumlah > $stok) {
                DB::rollBack();

                return back()->withInput()->with(
                    'error',
                    'Stok tidak cukup untuk produk '
                    . $showroom->produksi->namaproduk
                );
            }

            // Ambil jenis diskon yang dipilih kasir.
            $jenisDiskon = $request->jenis_diskon[$i];

            if ($jenisDiskon === 'partai') {
                $persenDiskon = (int) ($showroom->discount_partai ?? 0);
            } else {
                $persenDiskon = (int) ($showroom->discount ?? 0);
            }

            $harga = (int) $showroom->produksi->hargajual;

            // Hitung diskon berdasarkan jenis pilihan kasir.
            $subtotal = $harga * $jumlah;
            $diskonPerBarang = (int) round(
                $harga * $persenDiskon / 100
            );
            $totalDiskon = $diskonPerBarang * $jumlah;
            $total = max(0, $subtotal - $totalDiskon);

            $grandtotal += $total;

            PenjualanDetailModel::create([
                'idpenjualan' => $penjualan->idpenjualan,
                'idproduk' => $idproduk,
                'namaproduk' => $showroom->produksi->namaproduk,
                'harga' => $harga,
                'jumlah_pembelian' => $jumlah,
                'jenis_diskon' => $jenisDiskon,
                'discount' => $persenDiskon,
                'total' => $total,
            ]);

            // Perbarui stok showroom.
            $showroom->stok_terjual =
                (int) $showroom->stok_terjual + $jumlah;

            $showroom->stok_sisa =
                (int) $showroom->stok_awal
                + (int) $showroom->stok_tambahan
                - (int) $showroom->stok_terjual;

            $showroom->save();
        }

        $penjualan->update([
            'grandtotal' => $grandtotal,
            'sisabayar' => max(0, $grandtotal - $dp),
            'kembali' => max(
                0,
                $bayar - ($dp > 0 ? $dp : $grandtotal)
            ),
            'statuspembayaran' => $dp > 0 ? 'DP' : 'Lunas',
        ]);

        DB::commit();

        return redirect('kasir/penjualandaftar/' . $notajual)
            ->with('success', 'Transaksi berhasil disimpan.');

    } catch (\Throwable $e) {
        DB::rollBack();

        report($e);

        return back()->withInput()->with(
            'error',
            'Transaksi gagal disimpan. Periksa data produk dan stok.'
        );
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
