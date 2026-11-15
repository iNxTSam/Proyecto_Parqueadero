<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 30);
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->string('entidad_tipo', 30)->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->boolean('leida')->default(false);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamps();

            $table->index('leida');
        });

        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('tipo', 30);
            $table->string('formato', 10);
            $table->string('ruta_archivo', 255)->nullable();
            $table->json('parametros')->nullable();
            $table->foreignId('generado_por_id')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();

            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
        Schema::dropIfExists('alertas');
    }
};
