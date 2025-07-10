<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpnameModel extends Model
{
    protected $table = 'stock_opname';
    protected $primaryKey = 'idstockopname';

    protected $fillable = [
        'showroom_id',
        'tanggal_opname',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
    ];

    public function showroom()
    {
        return $this->belongsTo(ShowroomModel::class, 'showroom_id', 'idshowroom');
    }
}
