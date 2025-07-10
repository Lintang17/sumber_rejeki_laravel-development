<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetailModel extends Model
{
    protected $table = 'penjualan_detail';
    protected $primaryKey = 'iddetail';

    protected $fillable = [
        'idpenjualan',
        'idproduk',
        'namaproduk',
        'harga',
        'jumlah_pembelian',
        'total',
    ];

    public function penjualan()
    {
        return $this->belongsTo(PenjualanModel::class, 'idpenjualan');
    }

    public function showroom()
    {
        return $this->belongsTo(ShowroomModel::class, 'idproduk', 'idshowroom');
    }
}
