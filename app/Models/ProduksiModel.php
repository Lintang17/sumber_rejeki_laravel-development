<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiModel extends Model
{
    protected $table = 'produksi'; // Nama tabel (karena tidak jamak)

    protected $primaryKey = 'idproduksi'; // Primary key

    public $timestamps = true; // Untuk created_at dan updated_at

    protected $fillable = [
        'idproduksi',
        'namaproduk',
        'stok',
        'stok_awal',
        'stok_tambahan',
        'hppestimasi',
        'hppfinal',
        'hargajual',
        'tanggalproduksi',
        'tanggal_update',
        'tanggalselesai',
        'fotoproduk',
        'deskripsiproduk',
        'status',
    ];
}
