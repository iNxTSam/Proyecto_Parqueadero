<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('vehiculo_id', 10);
            $table->string('propietario_id', 15);
            $table->foreignId('tarifa_id')->constrained('tarifas')->restrictOnDelete();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->foreignId('estado_id')->constrained('estados_contrato')->restrictOnDelete();
            $table->decimal('valor', 10, 2);
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->foreign('vehiculo_id')->references('placa')->on('vehiculos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('propietario_id')->references('id')->on('clientes')->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['vehiculo_id', 'estado_id']);
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
