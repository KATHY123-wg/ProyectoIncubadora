<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_venta extends Model
{
  use HasFactory;
  protected $table = 'detalle_ventas'; // nombre de la tabla en BD si no sigue la convención
  protected $primaryKey = null;
  public $incrementing = false;

  public $timestamps = false;
  protected $fillable = [
    'venta_id',
    'incubadora_id',
    'cantidad',
    'precio_unitario',

  ];
  public function venta()
  {
    return $this->belongsTo(Venta::class, 'venta_id');
  }
  //  public function incubadora() { return $this->belongsTo(Incubadora::class, 'incubadora_id'); 
  // 🔁 Relación: este detalle pertenece a una venta
  //public function venta1()
  // {
  //   return $this->belongsTo(Venta::class);
  // }

  // 🔁 Relación: este detalle pertenece a una incubadora
  public function incubadora()
  {
    return $this->belongsTo(Incubadora::class, 'incubadora_id');
  }
}
