<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->smallIncrements('id');

            // Cliente (avicultor) que compra la incubadora
            $table->unsignedSmallInteger('avicultor_id');
            $table->foreign('avicultor_id')->references('id')->on('usuarios')->onDelete('cascade');

            // Usuario del sistema que registró la venta (vendedor, admin, etc.)
            $table->unsignedSmallInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('cascade');

            // Fecha de la venta
            $table->dateTime('fecha_venta')->default(DB::raw('CURRENT_TIMESTAMP'));

            // Monto total en bolivianos
            $table->decimal('total_bs', 10, 2)->default(0);

            // Estado: 1 = activa, 0 = anulada
            $table->tinyInteger('estado')->default(1);

            // Timestamps en español
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrent()->useCurrentOnUpdate()->nullable(); // ✅ Recomendado
            $table->unsignedSmallInteger('modificado_por')->nullable();
            

});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
