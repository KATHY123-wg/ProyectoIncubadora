<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lectura_sensores extends Model
{
    use HasFactory;

    protected $table = 'lectura_sensores';

    public $timestamps = false; // tus columnas son personalizadas (fecha_registro/ultima_actualizacion)

    protected $fillable = [
        'proceso_id',
        'temperatura',
        'humedad',
        'fecha_hora',
        'error_motor',
        'error_foco',
        'error_sensor',
        'fecha_registro',
        'ultima_actualizacion',
    ];

    // Cada lectura pertenece a un proceso (tu modelo Incubacion)
    public function proceso()
{
    // Tu modelo de procesos se llama Incubacion y usa la tabla 'procesos'
    return $this->belongsTo(\App\Models\Incubacion::class, 'proceso_id');
}


    // Esta relación NO aplica directamente (la incubadora se obtiene vía proceso)
    // Elimínala para evitar confusión:
    // public function incubadora()
    // {
    //     return $this->belongsTo(Incubadora::class);
    // }
}
