<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeAspekKeuangan extends Model
{
    use HasFactory;

    protected $table = 'periode_aspek_keuangan';

    protected $fillable = [
        'perhitungan_kredit_id',
        'bulan',
        'tahun',
    ];
}
