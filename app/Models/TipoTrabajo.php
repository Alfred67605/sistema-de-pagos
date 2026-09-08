<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTrabajo extends Model
{
    protected $table = 'tipos_trabajo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];
}
