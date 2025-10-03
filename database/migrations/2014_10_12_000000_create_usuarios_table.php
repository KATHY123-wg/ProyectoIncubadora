<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nombre', 60);
            $table->string('apellido1', 60);
            $table->string('apellido2', 60)->nullable();
            $table->string('usuario', 60)->unique();
            $table->string('correo', 60)->unique()->nullable();
            $table->timestamp('correo_verificado_en')->nullable(); 
            $table->string('contraseña', 255);
            // Campos del cliente
            $table->string('ci_nit', 15)->unique();
            $table->string('telefono', 15);
            $table->string('direccion', 150)->nullable();

            $table->enum('rol', ['admin', 'avicultor', 'vendedor'])->default('avicultor');
            $table->tinyInteger('estado')->default(1);

            // Campos de tiempo en español
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate()->nullable(); // ✅ Recomendado

            $table->unsignedSmallInteger('modificado_por')->nullable();
            
            $table->rememberToken();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
