<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incubadoras', function (Blueprint $table) {
            $table->smallIncrements('id');

            $table->string('codigo', 20)->unique();     // Código único
            $table->string('descripcion');              // Descripción

            // Usuario asignado (opcional al crear)
            $table->unsignedSmallInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')
                  ->references('id')->on('usuarios')
                  ->nullOnDelete(); // SET NULL al borrar el usuario

            // Estado: 0=inactiva (por no estar asignada), 1=activa
            $table->tinyInteger('estado')->default(0);

            // Timestamps en español
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate();

            // Auditoría
            $table->unsignedSmallInteger('modificado_por')->nullable();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incubadoras');
    }
};
