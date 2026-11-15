<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuarioDemoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'cedula' => '1000000001',
                'nombre' => 'Super Administrador',
                'email' => 'superadmin@parqueadero.test',
                'rol_id' => 1,
            ],
            [
                'cedula' => '1000000002',
                'nombre' => 'Administrador',
                'email' => 'admin@parqueadero.test',
                'rol_id' => 2,
            ],
            [
                'cedula' => '1000000003',
                'nombre' => 'Vigilante',
                'email' => 'vigilante@parqueadero.test',
                'rol_id' => 3,
            ],
        ];

        foreach ($usuarios as $usuario) {
            Usuario::updateOrCreate(
                ['cedula' => $usuario['cedula']],
                $usuario + [
                    'password' => 'Demo12345!',
                    'activo' => true,
                ]
            );
        }
    }
}
