<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anticipo extends Model
{
    protected $fillable = ['trabajador_id', 'fecha', 'monto', 'saldo', 'pagado', 'observacion', 'dias_debe'];

    protected $casts = [
        'pagado' => 'boolean',
        'fecha' => 'date',
        'dias_debe' => 'decimal:2',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function pagos()
    {
        return $this->belongsToMany(Pago::class, 'pago_anticipo')
                    ->withPivot('monto_descontado')
                    ->withTimestamps();
    }

    public function getMontoLetrasAttribute()
    {
        return Pago::convertirNumeroALetras($this->monto);
    }
}
