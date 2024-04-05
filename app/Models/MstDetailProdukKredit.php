<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstDetailProdukKredit extends Model
{
    use HasFactory;
    protected $table = 'mst_detail_produk_kredit';

    protected $fillable = [
        'produk_kredit_id',
        'skema_kredit_id',
    ];

    public function skemaKredit()
    {
        return $this->belongsTo(MstSkemaKredit::class, 'skema_kredit_id', 'id');
    }
}
