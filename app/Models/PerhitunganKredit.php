<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerhitunganKredit extends Model
{
    use HasFactory;

    protected $table = 'perhitungan_kredit';
    protected $guarded = 'id';
    protected $fillable = [];

    public function mstItemPerhitunganKredit()
    {
        return $this->belongsTo(MstItemPerhitunganKredit::class, 'item_perhitungan_kredit_id', 'id');
    }
}
