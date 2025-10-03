<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incubadora extends Model
{
    use HasFactory;


    // Usa tus timestamps en español
    protected $connection = 'mysql_always'; 
    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'ultima_actualizacion';
    public $timestamps = false;
    protected $table = 'incubadoras'; // nombre de la tabla en BD si no sigue la convención

    protected $fillable = [
        'codigo',
        'descripcion',
        'usuario_id',
        'estado',
        'fecha_registro',
        'ultima_actualizacion',
        'modificado_por',
    ];

    protected $casts = [
        'estado' => 'integer',
        'usuario_id' => 'integer',
        'modificado_por' => 'integer',
        'fecha_registro' => 'datetime',
        'ultima_actualizacion' => 'datetime',
    ];
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function procesos()
    {
        return $this->hasMany(Incubacion::class, 'incubadora_id');
    }




    // Relación: esta incubadora puede estar en muchos detalles de venta
    public function detallesVenta()
    {
        return $this->hasMany(Detalle_venta::class);
    }

    // Usuario que creó este registro
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 🔍 Usuario que actualizó este registro
    public function actualizadoPor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
