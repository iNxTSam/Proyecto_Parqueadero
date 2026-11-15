<?php

namespace Tests\Feature;

use App\Models\Recibo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportesTest extends TestCase
{
    use RefreshDatabase;

    public function test_exporta_un_reporte_csv(): void
    {
        $superadmin = $this->actuarComo(Usuario::SUPERADMIN);

        $respuesta = $this->post(route('superadmin.reportes.generar'), [
            'tipo_reporte' => 'usuarios',
            'formato' => 'csv',
        ]);

        $respuesta->assertOk();
        $this->assertStringContainsString('text/csv', (string) $respuesta->headers->get('content-type'));
        $this->assertStringContainsString('Nombre', $respuesta->streamedContent());

        $this->assertDatabaseHas('reportes', [
            'tipo' => 'usuarios',
            'formato' => 'CSV',
            'generado_por_id' => $superadmin->id,
        ]);
    }

    public function test_exporta_un_reporte_pdf(): void
    {
        $this->actuarComo(Usuario::SUPERADMIN);

        $respuesta = $this->post(route('superadmin.reportes.generar'), [
            'tipo_reporte' => 'contratos',
            'formato' => 'pdf',
        ]);

        $respuesta->assertOk();
        $this->assertSame('application/pdf', $respuesta->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', (string) $respuesta->getContent());
    }

    public function test_filtra_los_ingresos_por_fecha(): void
    {
        $superadmin = $this->actuarComo(Usuario::SUPERADMIN);
        $cliente = $this->crearCliente(1);
        $vehiculo = $this->crearVehiculo($cliente);
        $contrato = $this->crearContrato($vehiculo);

        Recibo::create([
            'numero' => 'REC-VIEJO',
            'contrato_id' => $contrato->id,
            'fecha_emision' => today()->subYear(),
            'valor' => 1000,
            'estado' => 'pagado',
            'emitido_por_id' => $superadmin->id,
        ]);

        Recibo::create([
            'numero' => 'REC-NUEVO',
            'contrato_id' => $contrato->id,
            'fecha_emision' => today(),
            'valor' => 2000,
            'estado' => 'pagado',
            'emitido_por_id' => $superadmin->id,
        ]);

        $respuesta = $this->post(route('superadmin.reportes.generar'), [
            'tipo_reporte' => 'ingresos',
            'formato' => 'csv',
            'fecha_inicio' => today()->subMonth()->toDateString(),
            'fecha_fin' => today()->toDateString(),
        ]);

        $contenido = $respuesta->streamedContent();

        $this->assertStringContainsString('REC-NUEVO', $contenido);
        $this->assertStringNotContainsString('REC-VIEJO', $contenido);
    }

    public function test_rechaza_un_tipo_de_reporte_invalido(): void
    {
        $this->actuarComo(Usuario::SUPERADMIN);

        $this->post(route('superadmin.reportes.generar'), [
            'tipo_reporte' => 'inventado',
            'formato' => 'csv',
        ])->assertSessionHasErrors('tipo_reporte');
    }

    public function test_rechaza_una_fecha_final_anterior_a_la_inicial(): void
    {
        $this->actuarComo(Usuario::SUPERADMIN);

        $this->post(route('superadmin.reportes.generar'), [
            'tipo_reporte' => 'contratos',
            'formato' => 'csv',
            'fecha_inicio' => today()->toDateString(),
            'fecha_fin' => today()->subDay()->toDateString(),
        ])->assertSessionHasErrors('fecha_fin');
    }
}
