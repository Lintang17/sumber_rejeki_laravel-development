<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShowroomModel extends Model
{
    use SoftDeletes;

    protected $table = 'showroom';
    protected $primaryKey = 'idshowroom';

    protected $fillable = [
        'idproduk',
        'stok_awal',
        'stok_tambahan',
        'stok_total',
        'stok_terjual',
        'stok_sisa',
        'barcode',
        'link',
        'kodeqr',
        'tanggalmasuk',
    ];

    protected $dates = ['tanggalmasuk', 'deleted_at'];

    /**
     * Relasi ke model Produk jika diperlukan
     */
    public function produksi()
    {
        return $this->belongsTo(ProduksiModel::class, 'idproduk', 'idproduksi');
    }

    public function showrooms()
{
    return $this->hasMany(ShowroomModel::class, 'idproduk', 'idproduksi');
}
}
