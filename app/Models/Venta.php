<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'ventas'; // nombre de la tabla en BD si no sigue la convención

    protected $fillable = [
        'avicultor_id',
        'usuario_id',
        'fecha_venta',
        'total_bs',
        'estado',
        'fecha_registro',
        'ultima_actualizacion',
        'modificado_por',

    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    // 🔁 Relación: una venta tiene muchos detalles
    public function detalles()
    {
        return $this->hasMany(Detalle_venta::class);
    }


    public function avicultor()
    {
        return $this->belongsTo(User::class, 'avicultor_id');
    }

    public function cajero()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function incubadora()
    {
        return $this->belongsTo(Incubadora::class);
    }

    public function detalleVentas()
    {
        return $this->hasMany(Detalle_venta::class, 'venta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    public function detalle_ventas()
    {
    
        return $this->hasMany(Detalle_venta::class, 'venta_id');
    }
}
