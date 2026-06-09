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
        'no_hp',
        'alamat',
        'foto',
        'estimasi_awal',
        'estimasi_akhir',
        'keterangan',
        'status',
        'total',
        'hpp_final'

    ];

    public function detail()
    {
        return $this->hasMany(PoDetail::class, 'po_id');
    }
}