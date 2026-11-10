has una ultima revsion detdod cgamo, hasz la verificacion hasta el ultimo detl<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\TipoContrato;
use App\Models\TipoTrabajo;
use App\Models\FondoPago;
use App\Models\Anticipo;
use App\Models\Pago;
use App\Models\ServicioExterno;
use App\Models\CajaMineralRecarga;
use App\Models\TransaccionMineral;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with comprehensive data across all modules.
     */
    public function run(): void
    {
        // 1. Usuarios del Sistema
        $users = [
            ['email' => 'admin@mina.com', 'name' => 'Administrador Minero', 'password' => 'admin123'],
            ['email' => 'contador@mina.com', 'name' => 'Lic. Carla Mendoza (Contabilidad)', 'password' => 'admin123'],
            ['email' => 'operaciones@mina.com', 'name' => 'Ing. Roberto Siles (Jefe de Mina)', 'password' => 'admin123'],
            ['email' => 'comercial@mina.com', 'name' => 'Encargado de Comercialización', 'password' => 'admin123'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'email_verified_at' => now(),
                ]
            );
        }

        // 2. Bocaminas
        $sanjose = Bocamina::create([
            'nombre' => 'Bocamina San José',
            'descripcion' => 'Sector norte, nivel 120. Veta principal rica en plata (Ag) y plomo (Pb).',
        ]);
        $rosario = Bocamina::create([
            'nombre' => 'Bocamina Rosario',
            'descripcion' => 'Galería central profunda, nivel 240. Extracción intensiva de complejo de zinc (Zn).',
        ]);
        $santamaria = Bocamina::create([
            'nombre' => 'Bocamina Santa María',
            'descripcion' => 'Frente de exploración y desarrollo en la sección sur, veta de estaño y plata.',
        ]);
        $sancristobal = Bocamina::create([
            'nombre' => 'Bocamina San Cristóbal',
            'descripcion' => 'Veta alta polimetálica Pb-Ag-Zn con extracción por cuadros y buzón.',
        ]);
        $socavon = Bocamina::create([
            'nombre' => 'Bocamina Virgen del Socavón',
            'descripcion' => 'Recorte este, galería mecanizada de alta ley en concentrados de zinc y estaño.',
        ]);
        $porvenir = Bocamina::create([
            'nombre' => 'Bocamina Porvenir',
            'descripcion' => 'Rampa principal de extracción mecanizada, ventilación y desagüe general.',
        ]);

        // 3. Catálogo de Tipos de Contrato
        $cSaco     = TipoContrato::create(['nombre' => 'Por saco', 'estado' => 'activo']);
        $cVolq     = TipoContrato::create(['nombre' => 'Por volqueta', 'estado' => 'activo']);
        $cMetro    = TipoContrato::create(['nombre' => 'Por metro avanzado', 'estado' => 'activo']);
        $cViaje    = TipoContrato::create(['nombre' => 'Por viaje', 'estado' => 'activo']);
        $cMensual  = TipoContrato::create(['nombre' => 'Mensual', 'estado' => 'activo']);
        $cDiario   = TipoContrato::create(['nombre' => 'Diario', 'estado' => 'activo']);
        $cOtro     = TipoContrato::create(['nombre' => 'Otro', 'estado' => 'activo']);
        $cTonelada = TipoContrato::create(['nombre' => 'Por tonelada extraída', 'estado' => 'activo']);

        // 4. Catálogo de Tipos de Trabajo
        $tiposTrabajo = [
            ['nombre' => 'Jornal', 'descripcion' => 'Pago por día trabajado o jornal realizado en interior o exterior mina', 'estado' => 'activo'],
            ['nombre' => 'Volqueta (Por Viaje)', 'descripcion' => 'Flete / Servicio de volqueta pagado por viaje realizado', 'estado' => 'activo'],
            ['nombre' => 'Pala Cargadora (Por Hora)', 'descripcion' => 'Operación de pala cargadora / maquinaria pagada por hora', 'estado' => 'activo'],
            ['nombre' => 'Pala Cargadora (Por Volquetada)', 'descripcion' => 'Carguío con pala cargadora pagado por volquetada cargada', 'estado' => 'activo'],
            ['nombre' => 'Carguío', 'descripcion' => 'Trabajo de carguío de mineral o material de desmonte', 'estado' => 'activo'],
            ['nombre' => 'Descarguío', 'descripcion' => 'Trabajo de descarguío de volqueta o sacos en cancha', 'estado' => 'activo'],
            ['nombre' => 'Transporte / Flete', 'descripcion' => 'Servicio de transporte y flete de material al ingenio', 'estado' => 'activo'],
            ['nombre' => 'Maquinaria (Por Hora)', 'descripcion' => 'Operación de maquinaria pesada por hora', 'estado' => 'activo'],
            ['nombre' => 'Mantenimiento', 'descripcion' => 'Mantenimiento preventivo y correctivo de herramientas y maquinaria', 'estado' => 'activo'],
            ['nombre' => 'Perforación', 'descripcion' => 'Perforación de pozo o avance de galería con perforadora neumática', 'estado' => 'activo'],
            ['nombre' => 'Limpieza', 'descripcion' => 'Limpieza de bocamina, cunetas e interiores de galería', 'estado' => 'activo'],
            ['nombre' => 'Otro', 'descripcion' => 'Otros tipos de trabajos generales autorizados', 'estado' => 'activo'],
            ['nombre' => 'Enmaderado y Sostenimiento', 'descripcion' => 'Armado de cuadros de madera y aseguramiento de galerías', 'estado' => 'activo'],
            ['nombre' => 'Bombeo y Drenaje', 'descripcion' => 'Operación de bombas sumergibles para evacuación de agua de mina', 'estado' => 'activo'],
        ];
        foreach ($tiposTrabajo as $tt) {
            TipoTrabajo::firstOrCreate(['nombre' => $tt['nombre']], $tt);
        }

        // 5. Personal y Contratos (Trabajadores)
        $juan = Trabajador::create([
            'codigo' => 'CON-1001', 'ci' => '5938201-LP', 'nombre' => 'Juan Pérez Mamani',
            'telefono' => '71234567', 'rol' => 'contratista', 'bocamina_id' => $sanjose->id,
            'tipo_contrato_id' => $cMetro->id, 'fecha_contrato' => Carbon::today()->subMonths(6)->toDateString(),
            'tarifa_acordada' => 500.00, 'estado' => 'activo',
            'observaciones' => 'Contratista principal de avance en veta norte',
        ]);
        $pedro = Trabajador::create([
            'codigo' => 'PER-1002', 'ci' => '4829301-OR', 'nombre' => 'Pedro Quispe Mamani',
            'telefono' => '72198765', 'rol' => 'chofer', 'bocamina_id' => $rosario->id,
            'tipo_contrato_id' => $cVolq->id, 'fecha_contrato' => Carbon::today()->subMonths(5)->toDateString(),
            'tarifa_acordada' => 150.00, 'estado' => 'activo',
            'observaciones' => 'Chofer titular de volqueta de alto tonelaje',
        ]);
        $luis = Trabajador::create([
            'codigo' => 'PER-1003', 'ci' => '6910293-PT', 'nombre' => 'Luis Alberto Flores',
            'telefono' => '73204918', 'rol' => 'ayudante', 'bocamina_id' => $sanjose->id,
            'tipo_contrato_id' => $cSaco->id, 'fecha_contrato' => Carbon::today()->subMonths(5)->toDateString(),
            'tarifa_acordada' => 12.50, 'estado' => 'activo',
            'observaciones' => 'Ayudante de ensacado y selección en cancha',
        ]);
        $mario = Trabajador::create([
            'codigo' => 'PER-1004', 'ci' => '3928103-LP', 'nombre' => 'Mario Choque Condori',
            'telefono' => '70129384', 'rol' => 'sereno', 'bocamina_id' => $santamaria->id,
            'tipo_contrato_id' => $cMensual->id, 'fecha_contrato' => Carbon::today()->subMonths(8)->toDateString(),
            'tarifa_acordada' => 3500.00, 'estado' => 'inactivo',
            'observaciones' => 'Ex-sereno turno nocturno Bocamina Santa María',
        ]);
        $kevin = Trabajador::create([
            'codigo' => 'CON-1005', 'ci' => '7384920-OR', 'nombre' => 'Kevin Mamani Huarachi',
            'telefono' => '74125896', 'rol' => 'contratista', 'bocamina_id' => $rosario->id,
            'tipo_contrato_id' => $cMetro->id, 'fecha_contrato' => Carbon::today()->subMonths(4)->toDateString(),
            'tarifa_acordada' => 550.00, 'estado' => 'activo',
            'observaciones' => 'Cuadrilla de perforación y voladura nivel 240',
        ]);
        $leonardo = Trabajador::create([
            'codigo' => 'PER-1006', 'ci' => '6473829-PT', 'nombre' => 'Leonardo Copa Villca',
            'telefono' => '75236987', 'rol' => 'perforista', 'bocamina_id' => $sancristobal->id,
            'tipo_contrato_id' => $cDiario->id, 'fecha_contrato' => Carbon::today()->subMonths(4)->toDateString(),
            'tarifa_acordada' => 180.00, 'estado' => 'activo',
            'observaciones' => 'Perforista especialista en chimeneas y recortes',
        ]);
        $miguel = Trabajador::create([
            'codigo' => 'PER-1007', 'ci' => '5291048-LP', 'nombre' => 'Miguel Ángel Ticona',
            'telefono' => '76347891', 'rol' => 'operador', 'bocamina_id' => $porvenir->id,
            'tipo_contrato_id' => $cDiario->id, 'fecha_contrato' => Carbon::today()->subMonths(4)->toDateString(),
            'tarifa_acordada' => 220.00, 'estado' => 'activo',
            'observaciones' => 'Operador de pala cargadora Scooptram y winche',
        ]);
        $nelson = Trabajador::create([
            'codigo' => 'PER-1008', 'ci' => '8192034-OR', 'nombre' => 'Nelson Chambi Nina',
            'telefono' => '77458912', 'rol' => 'carrero', 'bocamina_id' => $socavon->id,
            'tipo_contrato_id' => $cSaco->id, 'fecha_contrato' => Carbon::today()->subMonths(3)->toDateString(),
            'tarifa_acordada' => 15.00, 'estado' => 'activo',
            'observaciones' => 'Extracción de mineral en carros metaleros',
        ]);
        $oscar = Trabajador::create([
            'codigo' => 'PER-1009', 'ci' => '5019283-CB', 'nombre' => 'Oscar Yucra Apaza',
            'telefono' => '78569123', 'rol' => 'chofer', 'bocamina_id' => $sancristobal->id,
            'tipo_contrato_id' => $cViaje->id, 'fecha_contrato' => Carbon::today()->subMonths(3)->toDateString(),
            'tarifa_acordada' => 180.00, 'estado' => 'activo',
            'observaciones' => 'Transporte de carga bruta a ingenio',
        ]);
        $ramiro = Trabajador::create([
            'codigo' => 'CON-1010', 'ci' => '4192837-PT', 'nombre' => 'Ramiro Colque Huanca',
            'telefono' => '79671234', 'rol' => 'contratista', 'bocamina_id' => $santamaria->id,
            'tipo_contrato_id' => $cTonelada->id, 'fecha_contrato' => Carbon::today()->subMonths(3)->toDateString(),
            'tarifa_acordada' => 120.00, 'estado' => 'activo',
            'observaciones' => 'Contratista de explotación por tonelada extraída',
        ]);
        $sergio = Trabajador::create([
            'codigo' => 'PER-1011', 'ci' => '6029384-OR', 'nombre' => 'Sergio Condori Llanos',
            'telefono' => '71829304', 'rol' => 'sereno', 'bocamina_id' => $socavon->id,
            'tipo_contrato_id' => $cMensual->id, 'fecha_contrato' => Carbon::today()->subMonths(2)->toDateString(),
            'tarifa_acordada' => 3200.00, 'estado' => 'activo',
            'observaciones' => 'Responsable de seguridad y control de polvorín',
        ]);
        $victor = Trabajador::create([
            'codigo' => 'PER-1012', 'ci' => '7492018-LP', 'nombre' => 'Víctor Hugo Callisaya',
            'telefono' => '72930415', 'rol' => 'mecanico', 'bocamina_id' => $porvenir->id,
            'tipo_contrato_id' => $cMensual->id, 'fecha_contrato' => Carbon::today()->subMonths(2)->toDateString(),
            'tarifa_acordada' => 4200.00, 'estado' => 'activo',
            'observaciones' => 'Mecánico electromecánico de compresoras y bombas',
        ]);
        $walter = Trabajador::create([
            'codigo' => 'PER-1013', 'ci' => '5839201-OR', 'nombre' => 'Walter Paco Mendoza',
            'telefono' => '73041526', 'rol' => 'ayudante', 'bocamina_id' => $rosario->id,
            'tipo_contrato_id' => $cDiario->id, 'fecha_contrato' => Carbon::today()->subMonths(2)->toDateString(),
            'tarifa_acordada' => 130.00, 'estado' => 'activo',
            'observaciones' => 'Ayudante general de enmaderado y ventilación',
        ]);

        // 6. Fondos de Caja Chica (Módulo 1)
        FondoPago::create(['fecha' => Carbon::today()->subMonths(4)->startOfMonth()->toDateString(), 'monto' => 30000.00, 'observacion' => 'Apertura de Caja Chica Operativa']);
        FondoPago::create(['fecha' => Carbon::today()->subMonths(3)->startOfMonth()->toDateString(), 'monto' => 35000.00, 'observacion' => 'Fondeo Mensual para Planillas y Adelantos']);
        FondoPago::create(['fecha' => Carbon::today()->subMonths(2)->startOfMonth()->toDateString(), 'monto' => 40000.00, 'observacion' => 'Reposición de Fondos Liquidación Minera']);
        FondoPago::create(['fecha' => Carbon::today()->subMonth()->startOfMonth()->toDateString(), 'monto' => 45000.00, 'observacion' => 'Inyección de Liquidez Operativa y Servicios']);
        FondoPago::create(['fecha' => Carbon::today()->startOfMonth()->toDateString(), 'monto' => 55000.00, 'observacion' => 'Apertura de Fondos Operativos del Mes Actual']);

        // 7. Anticipos (Adelantos)
        $antPedro = Anticipo::create([
            'trabajador_id' => $pedro->id,
            'fecha' => Carbon::today()->subDays(25)->toDateString(),
            'monto' => 500.00, 'saldo' => 0.00, 'pagado' => true,
            'observacion' => 'Adelanto quincenal para gastos familiares (Descontado)', 'dias_debe' => 0,
        ]);
        $antKevin = Anticipo::create([
            'trabajador_id' => $kevin->id,
            'fecha' => Carbon::today()->subDays(18)->toDateString(),
            'monto' => 1000.00, 'saldo' => 400.00, 'pagado' => false,
            'observacion' => 'Adelanto para repuestos de perforadora (Parcialmente descontado)', 'dias_debe' => 1.0,
        ]);
        Anticipo::create([
            'trabajador_id' => $juan->id,
            'fecha' => Carbon::today()->subDays(7)->toDateString(),
            'monto' => 500.00, 'saldo' => 500.00, 'pagado' => false,
            'observacion' => 'Anticipo en efectivo a cuenta de avance semanal', 'dias_debe' => 1.0,
        ]);
        Anticipo::create([
            'trabajador_id' => $luis->id,
            'fecha' => Carbon::today()->subDays(5)->toDateString(),
            'monto' => 250.00, 'saldo' => 250.00, 'pagado' => false,
            'observacion' => 'Adelanto para pasajes y víveres', 'dias_debe' => 2.0,
        ]);
        Anticipo::create([
            'trabajador_id' => $oscar->id,
            'fecha' => Carbon::today()->subDays(3)->toDateString(),
            'monto' => 600.00, 'saldo' => 600.00, 'pagado' => false,
            'observacion' => 'Adelanto a cuenta de viajes de transporte a ingenio', 'dias_debe' => 3.0,
        ]);

        // 8. Pagos e Ítems de Liquidación
        $pagoPedro = Pago::create([
            'trabajador_id' => $pedro->id,
            'fecha' => Carbon::today()->subDays(20)->toDateString(),
            'numero_nota' => 'LIQ-2026-001',
            'tarifa_pago' => 150.00, 'cantidad_trabajada' => 20.00,
            'tipo_contrato_nombre' => 'Por volqueta',
            'subtotal' => 3000.00, 'bonos' => 200.00, 'descuentos' => 50.00,
            'anticipos_descontados' => 500.00, 'neto' => 2650.00, 'monto_pagado' => 2650.00,
            'saldo_pendiente' => 0.00, 'saldo_liquidado' => true,
            'tipo_cambio' => 6.96, 'observacion' => 'Liquidación quincena anterior',
            'metodo_pago' => 'efectivo', 'entregado_por' => 'Administrador Minero',
        ]);
        $pagoPedro->items()->create([
            'tipo_trabajo' => 'Volqueta (Por Viaje)',
            'descripcion' => '20 viajes de mineral bruto Bocamina Rosario',
            'cantidad' => 20, 'precio_unitario' => 150.00, 'subtotal' => 3000.00,
        ]);
        $pagoPedro->anticipos()->attach($antPedro->id, ['monto_descontado' => 500.00]);

        $pagoKevin = Pago::create([
            'trabajador_id' => $kevin->id,
            'fecha' => Carbon::today()->subDays(12)->toDateString(),
            'numero_nota' => 'LIQ-2026-002',
            'tarifa_pago' => 550.00, 'cantidad_trabajada' => 5.00,
            'tipo_contrato_nombre' => 'Por metro avanzado',
            'subtotal' => 2750.00, 'bonos' => 0.00, 'descuentos' => 0.00,
            'anticipos_descontados' => 600.00, 'neto' => 2150.00, 'monto_pagado' => 2150.00,
            'saldo_pendiente' => 0.00, 'saldo_liquidado' => true,
            'tipo_cambio' => 6.96, 'observacion' => 'Liquidación avance con amortización parcial de anticipo',
            'metodo_pago' => 'efectivo', 'entregado_por' => 'Administrador Minero',
        ]);
        $pagoKevin->items()->create([
            'tipo_trabajo' => 'Perforación',
            'descripcion' => 'Avance de 5 metros lineales en recorte sur',
            'cantidad' => 5, 'precio_unitario' => 550.00, 'subtotal' => 2750.00,
        ]);
        $pagoKevin->anticipos()->attach($antKevin->id, ['monto_descontado' => 600.00]);

        $pagoJuan = Pago::create([
            'trabajador_id' => $juan->id,
            'fecha' => Carbon::today()->subDays(2)->toDateString(),
            'numero_nota' => 'LIQ-2026-003',
            'tarifa_pago' => 500.00, 'cantidad_trabajada' => 6.00,
            'tipo_contrato_nombre' => 'Por metro avanzado',
            'subtotal' => 3000.00, 'bonos' => 200.00, 'descuentos' => 0.00,
            'anticipos_descontados' => 0.00, 'neto' => 3200.00, 'monto_pagado' => 3200.00,
            'saldo_pendiente' => 0.00, 'saldo_liquidado' => true,
            'tipo_cambio' => 6.96, 'observacion' => 'Liquidación avance 6 metros Bocamina San José',
            'metodo_pago' => 'efectivo', 'entregado_por' => 'Administrador Minero',
        ]);
        $pagoJuan->items()->create([
            'tipo_trabajo' => 'Por metro avanzado',
            'descripcion' => 'Avance en galería norte veta principal (6 m)',
            'cantidad' => 6, 'precio_unitario' => 500.00, 'subtotal' => 3000.00,
        ]);

        // 9. Servicios Externos
        ServicioExterno::create([
            'fecha' => Carbon::today()->subDays(14)->toDateString(),
            'numero_comprobante' => 'SE-2026-001',
            'tipo_servicio' => 'Volqueta / Transporte de Mineral (Flete)',
            'chofer_operador' => 'Hernán Valdivia Choque',
            'placa_maquinaria' => '4092-TKR',
            'bocamina_id' => $sanjose->id,
            'cantidad' => 10, 'unidad_medida' => 'viajes',
            'precio_unitario' => 220.00, 'monto_total' => 2200.00,
            'origen_destino' => 'Bocamina San José -> Ingenio Machacamarca',
            'metodo_pago' => 'efectivo', 'entregado_por' => 'Administrador Minero',
            'observacion' => 'Transporte de carga mineralizada de primera',
        ]);
        ServicioExterno::create([
            'fecha' => Carbon::today()->subDays(6)->toDateString(),
            'numero_comprobante' => 'SE-2026-002',
            'tipo_servicio' => 'Cargador Frontal (Pala Cargadora)',
            'chofer_operador' => 'Julio César Alarcón',
            'placa_maquinaria' => 'VOLVO-L120',
            'bocamina_id' => $rosario->id,
            'cantidad' => 12, 'unidad_medida' => 'horas',
            'precio_unitario' => 250.00, 'monto_total' => 3000.00,
            'origen_destino' => 'Cancha de Acopio Bocamina Rosario',
            'metodo_pago' => 'efectivo', 'entregado_por' => 'Administrador Minero',
            'observacion' => 'Carguío de lotes de concentrado de zinc a camiones',
        ]);

        // 10. Caja de Comercialización de Minerales (Módulo 2)
        CajaMineralRecarga::create([
            'fecha' => Carbon::today()->subMonths(2)->toDateString(),
            'monto' => 100000.00,
            'observacion' => 'Capital Inicial Operativo para Compra de Lotes de Mineral',
        ]);
        CajaMineralRecarga::create([
            'fecha' => Carbon::today()->subDays(10)->toDateString(),
            'monto' => 75000.00,
            'observacion' => 'Refuerzo de Caja Comercialización para Lotes de Alta Ley',
        ]);

        // 11. Transacciones de Minerales (Compras y Ventas con Análisis de Laboratorio)
        $lote1 = TransaccionMineral::create([
            'fecha' => Carbon::today()->subDays(15)->toDateString(),
            'tipo' => 'compra', 'presentacion' => 'Sacos',
            'cliente_proveedor' => 'Ingenio Minero Machacamarca',
            'bocamina_id' => $rosario->id,
            'cantidad' => 300, 'peso_bruto' => 15000, 'humedad_porcentaje' => 3.5, 'peso_neto_seco' => 15000,
            'precio_unidad' => 2.80, 'monto_total' => 40530.00,
            'cantidad_disponible' => 100, 'peso_disponible' => 5000,
            'observacion' => 'Concentrado limpio de zinc ensacado para exportación',
        ]);
        $lote1->analisis()->createMany([
            ['mineral' => 'Zinc', 'ley' => 51.60],
            ['mineral' => 'Plata', 'ley' => 6.40],
            ['mineral' => 'Plomo', 'ley' => 5.20],
        ]);

        $venta1 = TransaccionMineral::create([
            'fecha' => Carbon::today()->subDays(5)->toDateString(),
            'tipo' => 'venta', 'lote_id' => $lote1->id, 'presentacion' => 'Sacos',
            'cliente_proveedor' => 'Trafigura Operaciones Bolivia S.A.',
            'destino' => 'Puerto de Arica (Exportación)',
            'bocamina_id' => $rosario->id,
            'cantidad' => 200, 'peso_bruto' => 10000, 'humedad_porcentaje' => 3.0, 'peso_neto_seco' => 10000,
            'precio_unidad' => 3.75, 'monto_total' => 36375.00,
            'cantidad_disponible' => 0, 'peso_disponible' => 0,
            'observacion' => 'Exportación parcial de lote de zinc',
        ]);
        $venta1->analisis()->createMany([
            ['mineral' => 'Zinc', 'ley' => 52.00],
            ['mineral' => 'Plata', 'ley' => 6.50],
        ]);
    }
}
