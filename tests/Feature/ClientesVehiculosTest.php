<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientesVehiculosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_registra_un_cliente(): void
    {
        $this->actuarComo(Usuario::ADMIN);

        $this->post(route('admin.clientes.store'), [
            'id' => '700000001',
            'nombre' => 'Cliente Nuevo',
            'telefono' => '3001234567',
            'correo' => 'nuevo@test.com',
            'rol_id' => 2,
            'activo' => '1',
        ])->assertRedirect(route('admin.clientes'));

        $this->assertDatabaseHas('clientes', ['id' => '700000001', 'nombre' => 'Cliente Nuevo']);
    }

    public function test_el_cliente_se_puede_editar_conservando_su_documento(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente(2, ['id' => '700000002']);

        $this->put(route('admin.clientes.update', $cliente), [
            'id' => '700000002',
            'nombre' => 'Cliente Editado',
            'telefono' => '3009999999',
            'correo' => 'editado@test.com',
            'rol_id' => 2,
            'activo' => '1',
        ])->assertRedirect(route('admin.clientes'));

        $this->assertDatabaseHas('clientes', ['id' => '700000002', 'nombre' => 'Cliente Editado']);
    }

    public function test_no_se_puede_repetir_el_documento_de_un_cliente(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $this->crearCliente(2, ['id' => '700000003']);

        $this->post(route('admin.clientes.store'), [
            'id' => '700000003',
            'nombre' => 'Duplicado',
            'rol_id' => 2,
            'activo' => '1',
        ])->assertSessionHasErrors('id');
    }

    public function test_el_admin_registra_un_vehiculo(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente();

        $this->post(route('admin.vehiculos.store'), [
            'placa' => 'QWE123',
            'tipo_vehiculo_id' => 1,
            'propietario_id' => $cliente->id,
            'color' => 'Blanco',
            'marca' => 'Renault',
            'modelo' => '2021',
            'activo' => '1',
        ])->assertRedirect(route('admin.vehiculos'));

        $this->assertDatabaseHas('vehiculos', ['placa' => 'QWE123', 'propietario_id' => $cliente->id]);
    }

    public function test_el_vehiculo_se_puede_editar_conservando_su_placa(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente();
        $vehiculo = $this->crearVehiculo($cliente, ['placa' => 'QWE124']);

        $this->put(route('admin.vehiculos.update', $vehiculo), [
            'placa' => 'QWE124',
            'tipo_vehiculo_id' => 1,
            'propietario_id' => $cliente->id,
            'color' => 'Azul',
            'marca' => 'Renault',
            'modelo' => '2022',
            'activo' => '1',
        ])->assertRedirect(route('admin.vehiculos'));

        $this->assertDatabaseHas('vehiculos', ['placa' => 'QWE124', 'color' => 'Azul']);
    }

    public function test_no_se_puede_repetir_la_placa(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $cliente = $this->crearCliente();
        $this->crearVehiculo($cliente, ['placa' => 'QWE125']);

        $this->post(route('admin.vehiculos.store'), [
            'placa' => 'QWE125',
            'tipo_vehiculo_id' => 1,
            'propietario_id' => $cliente->id,
        ])->assertSessionHasErrors('placa');
    }

    public function test_no_se_puede_asignar_un_propietario_inexistente(): void
    {
        $this->actuarComo(Usuario::ADMIN);

        $this->post(route('admin.vehiculos.store'), [
            'placa' => 'QWE126',
            'tipo_vehiculo_id' => 1,
            'propietario_id' => '000000000',
        ])->assertSessionHasErrors('propietario_id');
    }
}
