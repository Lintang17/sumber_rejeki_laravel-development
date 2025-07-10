<!--<!?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockopnameModel extends Model
{
    protected $table = 'stockopname';
    protected $primaryKey = 'idstockopname';

    // timestamp
    public $timestamps = false;
    protected $fillable = [
        'idstockopname',
        'idproduk',
        'stoksistem',
        'stokgudang',
        'tanggalstockopname',
        'selisih',
        'waktuinputstockopname',
    ];

    public function produk()
    {
        return $this->belongsTo(ProdukModel::class, 'idproduk', 'idproduk')->withTrashed();
    }
}
