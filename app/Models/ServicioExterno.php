<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioExterno extends Model
{
    protected $table = 'servicios_externos';

    protected $fillable = [
        'fecha',
        'numero_comprobante',
        'tipo_servicio',
        'chofer_operador',
        'placa_maquinaria',
        'bocamina_id',
        'cantidad',
        'unidad_medida',
        'precio_unitario',
        'monto_total',
        'origen_destino',
        'metodo_pago',
        'entregado_por',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function bocamina()
    {
        return $this->belongsTo(Bocamina::class);
    }
}
