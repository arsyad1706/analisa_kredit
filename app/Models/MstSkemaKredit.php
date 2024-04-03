<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstSkemaKredit extends Model
{
    use HasFactory;
    protected $table = 'mst_skema_kredit';

    protected $fillable = [
        'name',
    ];
}
