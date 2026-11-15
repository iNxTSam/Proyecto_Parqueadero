<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrada_salida', function (Blueprint $table) {
            $table->id();
            $table->string('propietario_id', 15);
            $table->string('vehiculo_id', 10);
            $table->string('ticket', 20)->unique();
            $table->dateTime('fecha_entrada');
            $table->dateTime('fecha_salida')->nullable();
            $table->boolean('marcar_salida')->default(false);
            $table->foreignId('registrado_por_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('salida_registrada_por_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->foreign('propietario_id')->references('id')->on('clientes')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('vehiculo_id')->references('placa')->on('vehiculos')->cascadeOnUpdate()->restrictOnDelete();

            $table->index(['vehiculo_id', 'fecha_salida']);
            $table->index('fecha_entrada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrada_salida');
    }
};
