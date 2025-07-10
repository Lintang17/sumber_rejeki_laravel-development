<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukModel extends Model
{
    use SoftDeletes;
    protected $table = 'produk';
    protected $primaryKey = 'idproduk';

    // timestamp
    public $timestamps = false;
    protected $fillable = [
        'idproduk',
        'namaproduk',
        'hargajual',
        'stok',
        'fotoproduk',
        'barcode',
        'link',
        'kodeqr',
    ];

    // protected $dates = ['deleted_at'];
}
