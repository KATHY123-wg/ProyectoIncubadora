<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incubacion extends Model
{
    use HasFactory;


    public $timestamps = false;

    protected $table = 'procesos';

    protected $fillable = [
        'incubadora_id',
        'nombre',
        'cantidad_total_huevos',
        'cantidad_eclosionados',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'observaciones',
        'fecha_registro',
        'ultima_actualizacion',
        'modificado_por_id',
    ];

    public function incubadora()
    {
        return $this->belongsTo(\App\Models\Incubadora::class, 'incubadora_id');
    }

    public function lecturas()
    {
        // Importa correctamente la clase y usa la FK 'proceso_id'
        return $this->hasMany(\App\Models\Lectura_sensores::class, 'proceso_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function actualizadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }
}
