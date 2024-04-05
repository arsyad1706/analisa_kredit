<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstItemPerhitunganKredit extends Model
{
    use HasFactory;
    protected $table = 'mst_item_perhitungan_kredit';

    public function perhitunganKredit()
    {
        return $this->belongsTo(PerhitunganKredit::class, 'id', 'item_perhitungan_kredit_id');
    }
}
