<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('cedula', 20)->unique();
            $table->string('carnet', 20)->nullable();
            $table->string('contacto', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('password');
            $table->foreignId('rol_id')->constrained('roles')->restrictOnDelete();
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->index('rol_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
