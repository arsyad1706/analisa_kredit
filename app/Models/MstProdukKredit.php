<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstProdukKredit extends Model
{
    use HasFactory;
    protected $table = 'mst_produk_kredit';

    protected $fillable = [
        'name',
    ];
}
