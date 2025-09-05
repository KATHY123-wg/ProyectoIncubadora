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
        Schema::create('lectura_sensores', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Relación con procesos de incubación
            $table->unsignedSmallInteger('proceso_id');
            $table->foreign('proceso_id')->references('id')->on('procesos')->onDelete('cascade');

            // Datos de lectura
            $table->float('temperatura', 5, 2); // Temperatura en °C
            $table->float('humedad', 5, 2);     // Humedad en %

            $table->dateTime('fecha_hora');     // Fecha y hora de la lectura

            // Errores posibles
            $table->boolean('error_motor')->default(0);      // Falla en motor
            $table->boolean('error_foco')->default(0);       // Falla en foco o calefacción
            $table->boolean('error_sensor')->default(0);     // Falla en sensor DHT

            // Fechas de auditoría en español
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate()->nullable();



        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectura_sensores');
    }
};
 