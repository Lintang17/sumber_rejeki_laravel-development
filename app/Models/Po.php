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
        'estimasi_awal',
        'estimasi_akhir',
        'keterangan',
        'status',
        'tanggal_dikirim',
        'tanggal_selesai',
        'total',
        'dp',
        'sisa_pembayaran',
        'status_pembayaran',
        'metode_pembayaran'
    ];

    public function detail()
    {
        return $this->hasMany(PoDetail::class, 'po_id');
    }
}