<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryHapus extends Model
{
    protected $table = 'history_hapus';

    protected $fillable = [
        'user_id',
        'nama_user',
        'jenis_data',
        'data_id',
        'nama_data',
        'data_snapshot',
        'dihapus_pada',
        'dipulihkan_pada',
        'dihapus_permanen_pada',
    ];

    protected $casts = [
        'data_snapshot' => 'array',
        'dihapus_pada' => 'datetime',
        'dipulihkan_pada' => 'datetime',
        'dihapus_permanen_pada' => 'datetime',
    ];
}
