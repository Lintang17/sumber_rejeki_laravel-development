<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Po extends Model
{
    protected $table = 'po';

    protected $fillable = [

        'kode_po',
        'tanggal',
        'customer',
        'estimasi_awal',
        'estimasi_akhir',
        'keterangan',
        'status',
        'total'

    ];

    public function detail()
    {
        return $this->hasMany(PoDetail::class, 'po_id');
    }
}