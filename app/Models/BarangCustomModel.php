<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangCustomModel extends Model
{
    // Nama tabel (default 'barang_custom' sudah sesuai, tapi kalau beda bisa ditulis di sini)
    protected $table = 'barang_custom';

    // Primary key kalau bukan 'id'
    protected $primaryKey = 'idcustom';

    // Tipe primary key jika bukan integer (di sini bigint dianggap integer)
    protected $keyType = 'int';

    // Kalau primary key auto increment
    public $incrementing = true;

    // Timestamps: default true (created_at, updated_at), sudah ada di tabel, jadi true
    public $timestamps = true;

    // Field yang boleh diisi mass assignment
    protected $fillable = [
        'kodecustom',
        'tanggalcustom',
        'namapembeli',
        'notelp',
        'alamat',
        'deskripsi',
        'jumlah',
        'fotoproduk',
        'status',
        'hpp_estimasi',
        'hpp_final',
        'hargajual',
        'dp',
        'pelunasan',
        'tanggal_dp',
        'tanggal_lunas',
        'created_by',
    ];

    // Jika ingin otomatis casting tipe data tertentu
    protected $casts = [
        'tanggalcustom' => 'datetime',
        'tanggal_dp' => 'datetime',
        'tanggal_lunas' => 'datetime',
        'hpp_estimasi' => 'integer',
        'hpp_final' => 'integer',
        'hargajual' => 'integer',
        'dp' => 'integer',
        'pelunasan' => 'integer',
    ];

    // Jika mau buat accessor/mutator, misal buat status lebih user-friendly

    // Contoh relasi, misal ke user yang buat data
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
