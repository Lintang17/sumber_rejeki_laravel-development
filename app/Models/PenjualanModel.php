<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenjualanModel extends Model
{
    use SoftDeletes;

    protected $table = 'penjualan';
    protected $primaryKey = 'idpenjualan';

    protected $fillable = [
        'notajual',
        'kodenota',
        'namapembeli',
        'notelp',
        'alamat',
        'grandtotal',
        'dp',
        'bayar',
        'kembali',
        'tanggalpenjualan',
        'statuspengiriman',
        'statuspembayaran',
        'metodepembayaran',
        'sisabayar',
    ];

    protected $dates = ['tanggalpenjualan', 'deleted_at'];

    public function penjualandetail()
    {
        return $this->hasMany(PenjualanDetailModel::class, 'idpenjualan');
    }
}
