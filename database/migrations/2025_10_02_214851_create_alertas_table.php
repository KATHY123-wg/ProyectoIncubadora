<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alertas', function (Blueprint $table) {
            // Usamos increments normal para no quedarnos cortos con el volumen histórico
            $table->increments('id');

            // Tus llaves son SMALLINT: respetamos eso en las FKs
            $table->unsignedSmallInteger('incubadora_id');
            $table->unsignedSmallInteger('proceso_id')->nullable(); // puede no haber proceso (p.ej. SIN_LECTURAS)

            // Clasificación
            $table->string('tipo', 30);   // MOTOR, LAMPARA, SENSOR, TEMPERATURA, HUMEDAD, COMUNICACION, ENERGIA
            $table->string('codigo', 50); // MOTOR_ATASCADO, LAMPARA_SIN_CALENTAR, DHT22_SIN_DATOS, TEMP_FUERA_RANGO, HUM_FUERA_RANGO, SIN_LECTURAS

            // Severidad y mensaje
            $table->enum('nivel', ['info','warning','critical'])->default('warning');
            $table->string('mensaje', 255)->default('');

            // Contexto para reportes
            $table->double('valor_actual')->nullable(); // temperatura/humedad actual, si aplica
            $table->double('umbral')->nullable();       // umbral aplicado, si aplica

            // Gestión
            $table->enum('estado', ['abierta','resuelta','silenciada'])->default('abierta');
            $table->unsignedInteger('ocurrencias')->default(1);
            $table->timestamp('silenciada_hasta')->nullable();
            $table->timestamp('resuelta_en')->nullable();
            $table->unsignedSmallInteger('resuelta_por')->nullable();

            // Timestamps en español (coherente con tu BD)
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate()->nullable();

            // Índices útiles
            $table->index(['incubadora_id','estado']);
            $table->index(['codigo','estado']);
            $table->index(['proceso_id']);

            // FKs explícitas
            $table->foreign('incubadora_id')->references('id')->on('incubadoras')->cascadeOnDelete();
            $table->foreign('proceso_id')->references('id')->on('procesos')->nullOnDelete();
            $table->foreign('resuelta_por')->references('id')->on('usuarios')->nullOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alertas');
    }
};
