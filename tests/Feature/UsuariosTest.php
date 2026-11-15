<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsuariosTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_admin_crea_un_vigilante(): void
    {
        $this->actuarComo(Usuario::ADMIN);

        $this->post(route('admin.usuarios.store'), [
            'nombre' => 'Vigilante Nuevo',
            'cedula' => '555000111',
            'email' => 'vigilante@test.com',
            'contacto' => '3001112233',
            'rol_id' => Usuario::VIGILANTE,
            'password' => 'Secreta123',
            'activo' => '1',
        ])->assertRedirect(route('admin.usuarios'));

        $this->assertDatabaseHas('usuarios', [
            'cedula' => '555000111',
            'rol_id' => Usuario::VIGILANTE,
            'activo' => true,
        ]);
    }

    public function test_el_admin_no_puede_crear_superadministradores(): void
    {
        $this->actuarComo(Usuario::ADMIN);

        $this->post(route('admin.usuarios.store'), [
            'nombre' => 'Intruso',
            'cedula' => '555000112',
            'rol_id' => Usuario::SUPERADMIN,
            'password' => 'Secreta123',
        ])->assertSessionHasErrors('rol_id');

        $this->assertDatabaseMissing('usuarios', ['cedula' => '555000112']);
    }

    public function test_el_admin_edita_un_usuario_conservando_su_cedula(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $usuario = $this->crearUsuario(Usuario::VIGILANTE, ['cedula' => '555000113']);

        $this->put(route('admin.usuarios.update', $usuario), [
            'nombre' => 'Nombre Corregido',
            'cedula' => '555000113',
            'rol_id' => Usuario::VIGILANTE,
            'password' => '',
            'activo' => '1',
        ])->assertRedirect(route('admin.usuarios'));

        $this->assertDatabaseHas('usuarios', [
            'id' => $usuario->id,
            'nombre' => 'Nombre Corregido',
        ]);
    }

    public function test_el_admin_no_puede_modificar_al_superadministrador(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $superadmin = $this->crearUsuario(Usuario::SUPERADMIN, [
            'nombre' => 'Super Original',
            'cedula' => '555000114',
        ]);

        $this->from(route('admin.usuarios'))
            ->put(route('admin.usuarios.update', $superadmin), [
                'nombre' => 'Secuestrado',
                'cedula' => '999999999',
                'rol_id' => Usuario::ADMIN,
                'password' => 'Hackeado123',
                'activo' => '1',
            ])
            ->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('usuarios', [
            'id' => $superadmin->id,
            'nombre' => 'Super Original',
            'cedula' => '555000114',
        ]);
    }

    public function test_el_admin_no_puede_desactivar_su_propia_cuenta(): void
    {
        $admin = $this->actuarComo(Usuario::ADMIN);

        $this->from(route('admin.usuarios'))
            ->patch(route('admin.usuarios.toggle', $admin))
            ->assertRedirect(route('admin.usuarios'))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_el_admin_puede_desactivar_a_otro_usuario(): void
    {
        $this->actuarComo(Usuario::ADMIN);
        $vigilante = $this->crearUsuario(Usuario::VIGILANTE);

        $this->patch(route('admin.usuarios.toggle', $vigilante))
            ->assertRedirect(route('admin.usuarios'));

        $this->assertFalse($vigilante->fresh()->activo);
    }
}
