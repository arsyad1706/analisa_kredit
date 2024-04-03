<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonNasabahTemp extends Model
{
    use HasFactory;

    protected $table = 'temporary_calon_nasabah';

    protected $fillable = [
        'nama',
        'alamat_rumah',
        'alamat_usaha',
        'no_ktp',
        'no_telp',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'sektor_kredit',
        'jenis_usaha',
        'jumlah_kredit',
        'tujuan_kredit',
        'jaminan_kredit',
        'hubungan_bank',
        'verifikasi_umum',
        'id_user',
        'id_kabupaten',
        'id_kecamatan',
        'id_desa',
        'tenor_yang_diminta',
        'jaminan_tambahan',
        'skema_kredit',
        'type_loan',
        'produk_kredit_id',
        'skema_kredit_id',
        'skema_limit_id'
    ];
}
