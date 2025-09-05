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
 Schema::create('detalle_ventas', function (Blueprint $table) {
  

    $table->unsignedSmallInteger('venta_id');
    $table->unsignedSmallInteger('incubadora_id');

    $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
    $table->foreign('incubadora_id')->references('id')->on('incubadoras')->onDelete('cascade');

    // Campos adicionales
    $table->tinyInteger('cantidad');
    $table->decimal('precio_unitario', 10, 2);


    // Clave primaria compuesta
    $table->primary(['venta_id', 'incubadora_id']);     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
