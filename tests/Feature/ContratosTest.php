<?php

namespace Tests\Feature;

use App\Models\Contrato;
use App\Models\EstadoContrato;
use App\Models\Recibo;
use App\Models\Tarifa;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContratosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_crea_un_contrato_y_su_recibo(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $tarifa = $this->tarifaCarro();

        $this->post(route('admin.contratos.store'), [
            'vehiculo_id' => $vehiculo->placa,
            'tarifa_id' => $tarifa->id,
            'observaciones' => 'Contrato de prueba',
        ])->assertRedirect(route('admin.contratos'));

        $contrato = Contrato::firstOrFail();

        $this->assertSame($vehiculo->placa, $contrato->vehiculo_id);
        $this->assertSame($cliente->id, $contrato->propietario_id);
        $this->assertSame(EstadoContrato::ACTIVO, (int) $contrato->estado_id);
        $this->assertSame(1, Recibo::where('contrato_id', $contrato->id)->count());
    }

    public function test_no_se_pueden_tener_dos_contratos_activos_para_el_mismo_vehiculo(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $tarifa = $this->tarifaCarro();
        $this->crearContrato($vehiculo, $tarifa);

        $this->from(route('admin.contratos'))
            ->post(route('admin.contratos.store'), [
                'vehiculo_id' => $vehiculo->placa,
                'tarifa_id' => $tarifa->id,
            ])
            ->assertRedirect(route('admin.contratos'))
            ->assertSessionHas('error');

        $this->assertSame(1, Contrato::count());
    }

    public function test_renovar_finaliza_el_contrato_anterior_y_crea_uno_nuevo(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $tarifa = $this->tarifaCarro();
        $contrato = $this->crearContrato($vehiculo, $tarifa);

        $this->post(route('admin.contratos.renovar', $contrato), [
            'tarifa_id' => $tarifa->id,
        ])->assertRedirect(route('admin.contratos'));

        $this->assertSame(EstadoContrato::FINALIZADO, (int) $contrato->fresh()->estado_id);
        $this->assertSame(2, Contrato::count());

        $nuevo = Contrato::where('id', '!=', $contrato->id)->firstOrFail();
        $this->assertSame(EstadoContrato::ACTIVO, (int) $nuevo->estado_id);
        $this->assertTrue($nuevo->fecha_inicio->gt($contrato->fecha_inicio));
    }

    public function test_no_se_puede_usar_una_tarifa_de_otro_tipo_de_vehiculo(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente, ['tipo_vehiculo_id' => 1]);

        $tarifaMoto = Tarifa::create([
            'nombre' => 'Moto de prueba',
            'tipo' => 'Mes',
            'tipo_vehiculo_id' => 2,
            'valor' => 50000,
            'dias' => 30,
            'activa' => true,
        ]);

        $this->from(route('admin.contratos'))
            ->post(route('admin.contratos.store'), [
                'vehiculo_id' => $vehiculo->placa,
                'tarifa_id' => $tarifaMoto->id,
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, Contrato::count());
    }

    public function test_el_superadmin_no_puede_eliminar_una_tarifa_con_contratos(): void
    {
        $this->actuarComo(Usuario::SUPERADMIN);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $tarifa = $this->tarifaCarro();
        $this->crearContrato($vehiculo, $tarifa);

        $this->from(route('superadmin.tarifas'))
            ->delete(route('superadmin.tarifas.delete', $tarifa))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('tarifas', ['id' => $tarifa->id]);
    }
}
