<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pantalla_de_login_responde(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Iniciar sesión');
    }

    public function test_login_redirige_segun_el_rol(): void
    {
        $casos = [
            [Usuario::SUPERADMIN, '/superadmin/dashboard'],
            [Usuario::ADMIN, '/admin/dashboard'],
            [Usuario::VIGILANTE, '/vigilante'],
        ];

        foreach ($casos as [$rol, $destino]) {
            $usuario = $this->crearUsuario($rol);

            $this->post('/logging', [
                'numeroDocumento' => $usuario->cedula,
                'clave' => 'Demo12345!',
            ])->assertRedirect($destino);

            $this->assertAuthenticatedAs($usuario);

            $this->post('/logout')->assertRedirect('/');
            $this->assertGuest();
        }
    }

    public function test_login_con_password_incorrecta_falla(): void
    {
        $usuario = $this->crearUsuario(Usuario::ADMIN);

        $this->post('/logging', [
            'numeroDocumento' => $usuario->cedula,
            'clave' => 'incorrecta',
        ])->assertSessionHasErrors('numeroDocumento');

        $this->assertGuest();
    }

    public function test_un_usuario_inactivo_no_puede_entrar(): void
    {
        $usuario = $this->crearUsuario(Usuario::ADMIN, ['activo' => false]);

        $this->post('/logging', [
            'numeroDocumento' => $usuario->cedula,
            'clave' => 'Demo12345!',
        ])->assertSessionHasErrors('numeroDocumento');

        $this->assertGuest();
    }

    public function test_el_login_se_bloquea_despues_de_cinco_intentos(): void
    {
        $usuario = $this->crearUsuario(Usuario::ADMIN);

        for ($intento = 0; $intento < 5; $intento++) {
            $this->post('/logging', [
                'numeroDocumento' => $usuario->cedula,
                'clave' => 'incorrecta',
            ])->assertRedirect('/');
        }

        $this->post('/logging', [
            'numeroDocumento' => $usuario->cedula,
            'clave' => 'Demo12345!',
        ])->assertStatus(429);
    }

    public function test_logout_cierra_la_sesion(): void
    {
        $usuario = $this->crearUsuario(Usuario::ADMIN);

        $this->actingAs($usuario)
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }
}
