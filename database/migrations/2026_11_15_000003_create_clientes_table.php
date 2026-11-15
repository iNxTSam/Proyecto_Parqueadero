<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->string('nombre', 70);
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->foreignId('rol_id')->constrained('rolclientes')->restrictOnDelete();
            $table->timestamps();

            $table->index('rol_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
