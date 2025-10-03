<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->timestamp('ultima_revision_credenciales')->nullable()->after('ultima_actualizacion');
            $table->tinyInteger('requiere_cambio_password')->default(1)->after('ultima_revision_credenciales');
            $table->tinyInteger('forzar_rotacion_credenciales')->default(0)->after('requiere_cambio_password');
        });
    }
    public function down(): void {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['ultima_revision_credenciales', 'requiere_cambio_password', 'forzar_rotacion_credenciales']);
        });
    }
};


