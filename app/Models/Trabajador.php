<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $fillable = [
        'codigo',
        'ci', 
        'nombre', 
        'telefono', 
        'rol', 
        'estado', 
        'observaciones',
        'bocamina_id',
        'tipo_contrato_id',
        'fecha_contrato',
        'tarifa_acordada'
    ];

    protected $casts = [
        'fecha_contrato' => 'date',
    ];

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }

    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }

    public function anticipos()
    {
        return $this->hasMany(Anticipo::class);
    }

    public function contratos()
    {
        return $this->hasMany(Contrato::class)->with('tipoContrato')->orderByDesc('id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
