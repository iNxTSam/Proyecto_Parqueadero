<?php

namespace Database\Seeders;

use App\Models\Bahia;
use App\Models\TipoVehiculo;
use Illuminate\Database\Seeder;

class BahiasSeeder extends Seeder
{
    public function run(): void
    {
        $b = TipoVehiculo::pluck('id', 'nombre');

        $bahias = [
            ['numero' => 'A001', 'tipo_vehiculo_id' => $b['Carro'], 'capacidad_maxima' => 30, 'ubicacion' => 'Bloque A - piso 1'],
            ['numero' => 'A002', 'tipo_vehiculo_id' => $b['Carro'], 'capacidad_maxima' => 20, 'ubicacion' => 'Bloque A - piso 2'],
            ['numero' => 'M001', 'tipo_vehiculo_id' => $b['Motocicleta'], 'capacidad_maxima' => 30, 'ubicacion' => 'Zona motos'],
            ['numero' => 'M002', 'tipo_vehiculo_id' => $b['Motocicleta'], 'capacidad_maxima' => 15, 'ubicacion' => 'Zona motos - costado norte'],
            ['numero' => 'B001', 'tipo_vehiculo_id' => $b['Bicicleta'], 'capacidad_maxima' => 15, 'ubicacion' => 'Bicicletero principal'],
            ['numero' => 'E001', 'tipo_vehiculo_id' => $b['Bicicleta eléctrica'], 'capacidad_maxima' => 4, 'ubicacion' => 'Bicicletero eléctrico'],
        ];

        foreach ($bahias as $bahia) {
            Bahia::updateOrCreate(
                ['numero' => $bahia['numero']],
                $bahia + ['activa' => true]
            );
        }
    }
}
