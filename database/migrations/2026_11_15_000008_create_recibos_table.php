<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique();
            $table->foreignId('contrato_id')->constrained('contratos')->cascadeOnUpdate()->restrictOnDelete();
            $table->date('fecha_emision');
            $table->decimal('valor', 10, 2);
            $table->text('concepto')->nullable();
            $table->string('estado', 20)->default('pendiente');
            $table->foreignId('emitido_por_id')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->index('fecha_emision');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
