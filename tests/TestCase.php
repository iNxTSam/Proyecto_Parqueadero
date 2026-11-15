<?php

namespace Tests;

use App\Models\Bahia;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\EstadoContrato;
use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use App\Models\Usuario;
use App\Models\Vehiculo;
use Database\Seeders\CatalogosSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private static int $secuencia = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CatalogosSeeder::class);
    }

    protected function crearUsuario(int $rol = Usuario::ADMIN, array $datos = []): Usuario
    {
        self::$secuencia++;
        $numero = str_pad((string) self::$secuencia, 5, '0', STR_PAD_LEFT);

        return Usuario::create(array_merge([
            'nombre' => 'Usuario '.$numero,
            'cedula' => '80'.$numero,
            'email' => 'usuario'.$numero.'@parqueadero.test',
            'password' => 'Demo12345!',
            'rol_id' => $rol,
            'activo' => true,
        ], $datos));
    }

    protected function actuarComo(int $rol): Usuario
    {
        $usuario = $this->crearUsuario($rol);
        $this->actingAs($usuario);

        return $usuario;
    }

    protected function crearCliente(int $rolCliente = 2, array $datos = []): Cliente
    {
        self::$secuencia++;
        $numero = str_pad((string) self::$secuencia, 5, '0', STR_PAD_LEFT);

        return Cliente::create(array_merge([
            'id' => '90'.$numero,
            'nombre' => 'Cliente '.$numero,
            'telefono' => '3000000000',
            'correo' => 'cliente'.$numero.'@parqueadero.test',
            'activo' => true,
            'rol_id' => $rolCliente,
        ], $datos));
    }

    protected function crearVehiculo(Cliente $cliente, array $datos = []): Vehiculo
    {
        self::$secuencia++;
        $numero = str_pad((string) self::$secuencia, 3, '0', STR_PAD_LEFT);

        return Vehiculo::create(array_merge([
            'placa' => 'TST'.$numero,
            'tipo_vehiculo_id' => TipoVehiculo::where('nombre', 'Carro')->value('id'),
            'propietario_id' => $cliente->id,
            'color' => 'Gris',
            'marca' => 'Mazda',
            'modelo' => '2020',
            'activo' => true,
        ], $datos));
    }

    protected function crearContrato(Vehiculo $vehiculo, ?Tarifa $tarifa = null, array $datos = []): Contrato
    {
        $tarifa ??= $this->tarifaCarro();

        return Contrato::create(array_merge([
            'vehiculo_id' => $vehiculo->placa,
            'propietario_id' => $vehiculo->propietario_id,
            'tarifa_id' => $tarifa->id,
            'fecha_inicio' => today(),
            'fecha_fin' => today()->addDays($tarifa->dias),
            'estado_id' => EstadoContrato::ACTIVO,
            'valor' => $tarifa->valor,
            'created_by' => $this->crearUsuario(Usuario::ADMIN)->id,
        ], $datos));
    }

    protected function tarifaCarro(): Tarifa
    {
        $tipo = TipoVehiculo::where('nombre', 'Carro')->value('id');

        return Tarifa::firstOrCreate(
            ['nombre' => 'Tarifa de prueba carro'],
            [
                'tipo' => 'Mes',
                'tipo_vehiculo_id' => $tipo,
                'valor' => 100000,
                'dias' => 30,
                'activa' => true,
            ]
        );
    }

    protected function crearBahia(int $tipoVehiculoId, int $capacidad = 10): Bahia
    {
        self::$secuencia++;

        return Bahia::create([
            'numero' => 'Z'.str_pad((string) self::$secuencia, 3, '0', STR_PAD_LEFT),
            'tipo_vehiculo_id' => $tipoVehiculoId,
            'capacidad_maxima' => $capacidad,
            'activa' => true,
        ]);
    }
}
