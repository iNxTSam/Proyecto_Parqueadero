<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->string('placa', 10)->primary();
            $table->foreignId('tipo_vehiculo_id')->constrained('tipos_vehiculo')->restrictOnDelete();
            $table->string('propietario_id', 15);
            $table->foreignId('autorizado_por_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('color', 30)->nullable();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->boolean('estado_parqueo')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('propietario_id')
                ->references('id')
                ->on('clientes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->index('propietario_id');
            $table->index('tipo_vehiculo_id');
            $table->index('estado_parqueo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
