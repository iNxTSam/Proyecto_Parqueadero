<?php

namespace Database\Seeders;

use App\Models\Tarifa;
use App\Models\TipoVehiculo;
use Illuminate\Database\Seeder;

class TarifasSeeder extends Seeder
{
    public function run(): void
    {
        $carro = TipoVehiculo::where('nombre', 'Carro')->value('id');
        $moto = TipoVehiculo::where('nombre', 'Motocicleta')->value('id');

        $tarifas = [
            ['nombre' => 'Carro - Día', 'tipo' => 'Dia', 'tipo_vehiculo_id' => $carro, 'valor' => 8000, 'dias' => 1],
            ['nombre' => 'Carro - Semana', 'tipo' => 'Semana', 'tipo_vehiculo_id' => $carro, 'valor' => 45000, 'dias' => 7],
            ['nombre' => 'Carro - Quincena', 'tipo' => 'Quincena', 'tipo_vehiculo_id' => $carro, 'valor' => 80000, 'dias' => 15],
            ['nombre' => 'Carro - Mes', 'tipo' => 'Mes', 'tipo_vehiculo_id' => $carro, 'valor' => 140000, 'dias' => 30],
            ['nombre' => 'Moto - Día', 'tipo' => 'Dia', 'tipo_vehiculo_id' => $moto, 'valor' => 5000, 'dias' => 1],
            ['nombre' => 'Moto - Semana', 'tipo' => 'Semana', 'tipo_vehiculo_id' => $moto, 'valor' => 28000, 'dias' => 7],
            ['nombre' => 'Moto - Quincena', 'tipo' => 'Quincena', 'tipo_vehiculo_id' => $moto, 'valor' => 50000, 'dias' => 15],
            ['nombre' => 'Moto - Mes', 'tipo' => 'Mes', 'tipo_vehiculo_id' => $moto, 'valor' => 90000, 'dias' => 30],
        ];

        foreach ($tarifas as $tarifa) {
            Tarifa::updateOrCreate(
                ['nombre' => $tarifa['nombre']],
                $tarifa + ['activa' => true]
            );
        }
    }
}
