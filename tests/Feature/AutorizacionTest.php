<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorizacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_es_redirigido_al_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/vigilante')->assertRedirect(route('login'));
        $this->get('/superadmin/dashboard')->assertRedirect(route('login'));
    }

    public function test_el_vigilante_no_entra_a_admin_ni_superadmin(): void
    {
        $this->actuarComo(Usuario::VIGILANTE);

        $this->get('/admin/usuarios')->assertForbidden();
        $this->get('/superadmin/tarifas')->assertForbidden();
    }

    public function test_el_admin_no_entra_a_superadmin_ni_vigilante(): void
    {
        $this->actuarComo(Usuario::ADMIN);

        $this->get('/superadmin/dashboard')->assertForbidden();
        $this->get('/vigilante')->assertForbidden();
    }

    public function test_el_superadmin_no_entra_a_los_modulos_de_otros_roles(): void
    {
        $this->actuarComo(Usuario::SUPERADMIN);

        $this->get('/admin/usuarios')->assertForbidden();
        $this->get('/vigilante/entradas-salidas')->assertForbidden();
    }
}
