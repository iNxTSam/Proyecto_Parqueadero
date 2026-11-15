<?php

namespace Tests\Feature;

use App\Models\EntradaSalida;
use App\Models\TipoVehiculo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParqueoTest extends TestCase
{
    use RefreshDatabase;

    private function aprendizConContrato(): array
    {
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $this->crearBahia($vehiculo->tipo_vehiculo_id, 10);
        $this->crearContrato($vehiculo);

        return [$cliente, $vehiculo];
    }

    private function naturalSinContrato(): array
    {
        $cliente = $this->crearCliente(2);
        $vehiculo = $this->crearVehiculo($cliente, ['placa' => 'NAT001']);
        $this->crearBahia($vehiculo->tipo_vehiculo_id, 10);

        return [$cliente, $vehiculo];
    }

    public function test_el_vigilante_registra_el_ingreso_de_un_aprendiz_con_contrato(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        [$cliente, $vehiculo] = $this->aprendizConContrato();

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->placa,
        ])->assertRedirect(route('vigilante.entradas'));

        $entrada = EntradaSalida::firstOrFail();

        $this->assertSame($vehiculo->placa, $entrada->vehiculo_id);
        $this->assertNotEmpty($entrada->ticket);
        $this->assertNull($entrada->fecha_salida);
        $this->assertTrue($vehiculo->fresh()->estado_parqueo);
    }

    public function test_un_aprendiz_sin_contrato_no_puede_ingresar(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $this->crearBahia($vehiculo->tipo_vehiculo_id, 10);

        $this->from(route('vigilante.entradas'))
            ->post(route('vigilante.entradas.store'), [
                'propietario_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->placa,
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, EntradaSalida::count());
    }

    public function test_un_cliente_natural_puede_ingresar_sin_contrato(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        [$cliente, $vehiculo] = $this->naturalSinContrato();

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->placa,
        ])->assertRedirect(route('vigilante.entradas'));

        $this->assertSame(1, EntradaSalida::count());
    }

    public function test_no_se_puede_registrar_dos_veces_el_mismo_ingreso(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        [$cliente, $vehiculo] = $this->naturalSinContrato();

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->placa,
        ]);

        $this->from(route('vigilante.entradas'))
            ->post(route('vigilante.entradas.store'), [
                'propietario_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->placa,
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, EntradaSalida::count());
    }

    public function test_se_respeta_la_capacidad_de_la_bahia(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);

        $tipo = TipoVehiculo::create(['nombre' => 'Cuatrimoto']);
        $this->crearBahia($tipo->id, 1);

        $clienteA = $this->crearCliente(2);
        $vehiculoA = $this->crearVehiculo($clienteA, ['tipo_vehiculo_id' => $tipo->id, 'placa' => 'CAP001']);

        $clienteB = $this->crearCliente(2);
        $vehiculoB = $this->crearVehiculo($clienteB, ['tipo_vehiculo_id' => $tipo->id, 'placa' => 'CAP002']);

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $clienteA->id,
            'vehiculo_id' => $vehiculoA->placa,
        ])->assertRedirect(route('vigilante.entradas'));

        $this->from(route('vigilante.entradas'))
            ->post(route('vigilante.entradas.store'), [
                'propietario_id' => $clienteB->id,
                'vehiculo_id' => $vehiculoB->placa,
            ])
            ->assertSessionHas('error');

        $this->assertSame(1, EntradaSalida::count());
        $this->assertFalse($vehiculoB->fresh()->estado_parqueo);
    }

    public function test_la_salida_del_aprendiz_exige_el_ticket_correcto(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        [$cliente, $vehiculo] = $this->aprendizConContrato();

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->placa,
        ]);

        $entrada = EntradaSalida::firstOrFail();

        $this->from(route('vigilante.entradas'))
            ->put(route('vigilante.entradas.salida', $entrada), ['ticket' => 'INCORRECTO'])
            ->assertSessionHas('error');

        $this->assertNull($entrada->fresh()->fecha_salida);

        $this->put(route('vigilante.entradas.salida', $entrada), ['ticket' => $entrada->ticket])
            ->assertRedirect(route('vigilante.entradas'));

        $this->assertNotNull($entrada->fresh()->fecha_salida);
        $this->assertFalse($vehiculo->fresh()->estado_parqueo);
    }

    public function test_no_se_puede_registrar_dos_salidas(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        [$cliente, $vehiculo] = $this->naturalSinContrato();

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->placa,
        ]);

        $entrada = EntradaSalida::firstOrFail();

        $this->put(route('vigilante.entradas.salida', $entrada))->assertRedirect(route('vigilante.entradas'));

        $this->from(route('vigilante.entradas'))
            ->put(route('vigilante.entradas.salida', $entrada))
            ->assertSessionHas('error');
    }

    public function test_un_cliente_natural_puede_salir_sin_ticket(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        [$cliente, $vehiculo] = $this->naturalSinContrato();

        $this->post(route('vigilante.entradas.store'), [
            'propietario_id' => $cliente->id,
            'vehiculo_id' => $vehiculo->placa,
        ]);

        $entrada = EntradaSalida::firstOrFail();

        $this->put(route('vigilante.entradas.salida', $entrada))
            ->assertRedirect(route('vigilante.entradas'));

        $this->assertNotNull($entrada->fresh()->fecha_salida);
    }

    public function test_no_se_puede_ingresar_un_vehiculo_de_otro_propietario(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);
        $cliente = $this->crearCliente(2);
        $otro = $this->crearCliente(2);
        $vehiculo = $this->crearVehiculo($cliente, ['placa' => 'OTR001']);
        $this->crearBahia($vehiculo->tipo_vehiculo_id, 10);

        $this->from(route('vigilante.entradas'))
            ->post(route('vigilante.entradas.store'), [
                'propietario_id' => $otro->id,
                'vehiculo_id' => $vehiculo->placa,
            ])
            ->assertSessionHas('error');

        $this->assertSame(0, EntradaSalida::count());
    }
}
