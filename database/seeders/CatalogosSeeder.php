<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogosSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'nombre' => 'Super administrador', 'descripcion' => 'Administra tarifas, bahías, reportes y configuración.'],
            ['id' => 2, 'nombre' => 'Administrador', 'descripcion' => 'Gestiona usuarios, clientes, vehículos y contratos.'],
            ['id' => 3, 'nombre' => 'Vigilante', 'descripcion' => 'Registra entradas y salidas de vehículos.'],
        ];

        foreach ($roles as $rol) {
            DB::table('roles')->updateOrInsert(['id' => $rol['id']], $rol + ['created_at' => now(), 'updated_at' => now()]);
        }

        $rolesCliente = [
            ['id' => 1, 'nombre' => 'Aprendiz', 'descripcion' => 'Cliente con contrato de parqueadero.'],
            ['id' => 2, 'nombre' => 'Natural', 'descripcion' => 'Cliente ocasional, paga por permanencia.'],
        ];

        foreach ($rolesCliente as $rol) {
            DB::table('rolclientes')->updateOrInsert(['id' => $rol['id']], $rol + ['created_at' => now(), 'updated_at' => now()]);
        }

        $estados = [
            ['id' => 1, 'nombre' => 'Activo', 'descripcion' => 'Contrato vigente.'],
            ['id' => 2, 'nombre' => 'Cancelado', 'descripcion' => 'Contrato cancelado por el administrador.'],
            ['id' => 3, 'nombre' => 'Finalizado', 'descripcion' => 'Contrato cumplido o vencido.'],
        ];

        foreach ($estados as $estado) {
            DB::table('estados_contrato')->updateOrInsert(['id' => $estado['id']], $estado + ['created_at' => now(), 'updated_at' => now()]);
        }

        $tipos = [
            ['id' => 1, 'nombre' => 'Carro', 'descripcion' => 'Automóviles y camionetas.'],
            ['id' => 2, 'nombre' => 'Motocicleta', 'descripcion' => 'Motos de dos ruedas.'],
            ['id' => 3, 'nombre' => 'Bicicleta', 'descripcion' => 'Bicicletas convencionales.'],
            ['id' => 4, 'nombre' => 'Bicicleta eléctrica', 'descripcion' => 'Bicicletas con motor eléctrico.'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_vehiculo')->updateOrInsert(['id' => $tipo['id']], $tipo + ['created_at' => now(), 'updated_at' => now()]);
        }
    }
}
