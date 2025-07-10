<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembelianModel extends Model
{
    use SoftDeletes;
    protected $table = 'pembelian';
    protected $primaryKey = 'idpembelian';

    // timestamp
    public $timestamps = false;
    protected $fillable = [
        'idpembelian',
        'notabeli',
        'namabarang',
        'harga',
        'jumlah',
        'total',
        'grandtotal',
        'tanggalpembelian',
        'supplier',
        'waktuinputbeli',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
