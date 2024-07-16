<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';
    
    protected $fillable = [
        'estado_del_job','tiempo_de_ejecucion_del_job'
    ];

}
