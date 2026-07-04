<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    protected $table = 'po_detail';

    protected $fillable = [

        'po_id',
        'produk',
        'deskripsi',
        'foto',
        'qty',
        'hpp_estimasi_admin',
        'hpp_estimasi_gudang',
        'harga_jual',
        'subtotal',
        'hpp_final'

    ];

    public function po()
    {
        return $this->belongsTo(Po::class, 'po_id');
    }
}