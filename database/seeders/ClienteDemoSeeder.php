<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;

class ClienteDemoSeeder extends Seeder
{
    public function run(): void
    {
        $aprendiz = Cliente::updateOrCreate(
            ['id' => '900000001'],
            ['nombre' => 'Ana María Rodríguez', 'telefono' => '3001234567', 'correo' => 'ana@parqueadero.test', 'activo' => true, 'rol_id' => 1]
        );

        $natural = Cliente::updateOrCreate(
            ['id' => '900000002'],
            ['nombre' => 'Carlos Pérez', 'telefono' => '3109876543', 'correo' => 'carlos@parqueadero.test', 'activo' => true, 'rol_id' => 2]
        );

        $tipos = TipoVehiculo::pluck('id', 'nombre');

        Vehiculo::updateOrCreate(
            ['placa' => 'ABC123'],
            [
                'tipo_vehiculo_id' => $tipos['Carro'],
                'propietario_id' => $aprendiz->id,
                'color' => 'Gris',
                'marca' => 'Mazda',
                'modelo' => '2020',
                'activo' => true,
            ]
        );

        Vehiculo::updateOrCreate(
            ['placa' => 'XYZ45A'],
            [
                'tipo_vehiculo_id' => $tipos['Motocicleta'],
                'propietario_id' => $natural->id,
                'color' => 'Negro',
                'marca' => 'Yamaha',
                'modelo' => '2022',
                'activo' => true,
            ]
        );
    }
}
