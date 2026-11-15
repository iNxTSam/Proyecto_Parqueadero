<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginasTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_superadmin_ve_sus_paginas(): void
    {
        $this->actuarComo(Usuario::SUPERADMIN);

        foreach (['/superadmin/dashboard', '/superadmin/tarifas', '/superadmin/bahias', '/superadmin/reportes', '/superadmin/configuracion'] as $ruta) {
            $this->get($ruta)->assertOk();
        }
    }

    public function test_el_admin_ve_sus_paginas(): void
    {
        $this->actuarComo(Usuario::ADMIN);

        foreach (['/admin/dashboard', '/admin/usuarios', '/admin/contratos', '/admin/clientes', '/admin/vehiculos'] as $ruta) {
            $this->get($ruta)->assertOk();
        }
    }

    public function test_el_vigilante_ve_sus_paginas(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);

        foreach (['/vigilante', '/vigilante/entradas-salidas', '/vigilante/vehiculos'] as $ruta) {
            $this->get($ruta)->assertOk();
        }
    }

    public function test_una_ruta_inexistente_devuelve_404(): void
    {
        $this->get('/ruta-que-no-existe')->assertNotFound();
    }
}
