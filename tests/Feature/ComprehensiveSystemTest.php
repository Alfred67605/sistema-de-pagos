<?php

namespace Tests\Feature;

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
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComprehensiveSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_all_modules_completely(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(4, User::count());
        $this->assertGreaterThanOrEqual(6, Bocamina::count());
        $this->assertGreaterThanOrEqual(8, TipoContrato::count());
        $this->assertGreaterThanOrEqual(14, TipoTrabajo::count());
        $this->assertGreaterThanOrEqual(13, Trabajador::count());
        $this->assertGreaterThanOrEqual(5, FondoPago::count());
        $this->assertGreaterThanOrEqual(5, Anticipo::count());
        $this->assertGreaterThanOrEqual(3, Pago::count());
        $this->assertGreaterThanOrEqual(2, ServicioExterno::count());
        $this->assertGreaterThanOrEqual(2, CajaMineralRecarga::count());
        $this->assertGreaterThanOrEqual(2, TransaccionMineral::count());
    }

    public function test_servicios_externos_crud_and_receipt(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $bocamina = Bocamina::create(['nombre' => 'Bocamina Central', 'descripcion' => 'Test']);

        $storeData = [
            'fecha' => '2026-11-08',
            'numero_comprobante' => 'SE-TEST-001',
            'tipo_servicio' => 'Volqueta / Transporte de Mineral (Flete)',
            'chofer_operador' => 'Carlos Mamani',
            'placa_maquinaria' => '4092-XYZ',
            'bocamina_id' => $bocamina->id,
            'cantidad' => 5,
            'unidad_medida' => 'viajes',
            'precio_unitario' => 200.00,
            'monto_total' => 1000.00,
            'origen_destino' => 'Bocamina Central -> Ingenio',
            'metodo_pago' => 'efectivo',
            'observacion' => 'Flete de prueba',
        ];

        $response = $this->post(route('servicios-externos.store'), $storeData);
        $servicio = ServicioExterno::first();
        $this->assertNotNull($servicio);
        $response->assertRedirect(route('servicios-externos.show', $servicio->id));

        $this->get(route('servicios-externos.show', $servicio->id))->assertStatus(200);
        $this->get(route('servicios-externos.index'))->assertStatus(200);

        $updateData = array_merge($storeData, [
            'cantidad' => 6,
            'monto_total' => 1200.00,
        ]);
        $this->put(route('servicios-externos.update', $servicio->id), $updateData)
            ->assertRedirect(route('servicios-externos.index'));

        $this->assertDatabaseHas('servicios_externos', [
            'id' => $servicio->id,
            'monto_total' => 1200.00,
        ]);

        $this->delete(route('servicios-externos.destroy', $servicio->id))
            ->assertRedirect(route('servicios-externos.index'));
        $this->assertDatabaseMissing('servicios_externos', ['id' => $servicio->id]);
    }

    public function test_dashboard_and_all_reports_load_with_seeded_data(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@mina.com')->first();
        $this->actingAs($admin);

        $this->get(route('dashboard'))->assertStatus(200);
        $this->get(route('reportes.index', ['tab' => 'general']))->assertStatus(200);
        $this->get(route('reportes.index', ['tab' => 'trabajador']))->assertStatus(200);
        $this->get(route('reportes.index', ['tab' => 'bocamina']))->assertStatus(200);
        $this->get(route('reportes.index', ['tab' => 'anticipos']))->assertStatus(200);
        $this->get(route('reportes-comercializacion.index'))->assertStatus(200);
        $this->get(route('tipos-trabajo.index'))->assertStatus(200);
        $this->get(route('bocaminas.index'))->assertStatus(200);
    }
}

