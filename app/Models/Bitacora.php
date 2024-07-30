<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_completados','total_fallidos','descripcion','tipo','estado','codes'
    ];

    protected $casts = [
        'codes' => 'array',
      ];
}
