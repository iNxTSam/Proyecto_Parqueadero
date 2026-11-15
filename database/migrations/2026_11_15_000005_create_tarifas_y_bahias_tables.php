<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('tipo', 20);
            $table->foreignId('tipo_vehiculo_id')->constrained('tipos_vehiculo')->restrictOnDelete();
            $table->decimal('valor', 10, 2);
            $table->unsignedSmallInteger('dias')->default(30);
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->index('tipo_vehiculo_id');
        });

        Schema::create('bahias', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 10)->unique();
            $table->foreignId('tipo_vehiculo_id')->constrained('tipos_vehiculo')->restrictOnDelete();
            $table->unsignedSmallInteger('capacidad_maxima')->default(1);
            $table->string('ubicacion', 100)->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();

            $table->index('tipo_vehiculo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahias');
        Schema::dropIfExists('tarifas');
    }
};
