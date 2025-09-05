<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::create('procesos', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Relación con la incubadora
            $table->unsignedSmallInteger('incubadora_id');
            $table->foreign('incubadora_id')->references('id')->on('incubadoras')->onDelete('cascade');

            $table->string('nombre', 50); // Ejemplo: "Incubación Julio"

            // Nuevos campos solicitados
            $table->smallInteger('cantidad_total_huevos')->unsigned();     // Total puestos
            $table->smallInteger('cantidad_eclosionados')->nullable();; // Eclosionados

            // Fechas
            $table->dateTime('fecha_inicio')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('fecha_estimada')->storedAs('DATE_ADD(`fecha_inicio`, INTERVAL 21 DAY)');
            $table->dateTime('fecha_fin')->nullable();

            $table->text('observaciones')->nullable();

            $table->tinyInteger('estado')->default(1); // 1 = activo, 0 = finalizado/cancelado

            // Auditoría
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate()->nullable(); // ✅ Recomendado
            $table->unsignedSmallInteger('modificado_por')->nullable();
            
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos'); // corregido nombre de tabla también
    }
};
