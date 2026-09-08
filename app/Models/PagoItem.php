<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoItem extends Model
{
    protected $table = 'pago_items';

    protected $fillable = [
        'pago_id',
        'contrato_id',
        'tipo_trabajo',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad'       => 'decimal:2',
        'precio_unitario'=> 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }
}
