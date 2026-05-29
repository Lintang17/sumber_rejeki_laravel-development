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
        'qty',
        'hpp_estimasi',
        'harga_jual',
        'subtotal'

    ];

    public function po()
    {
        return $this->belongsTo(Po::class, 'po_id');
    }
}