<?php

namespace App\Models\Remote;

use Illuminate\Database\Eloquent\Model;

class RemoteLectura extends Model
{
    protected $connection = 'mysql_always';   // <- BD remota
    protected $table = 'lectura_sensores';
    public $timestamps = false;

    protected $fillable = [
        'proceso_id','temperatura','humedad','fecha_hora',
        'error_motor','error_foco','error_sensor',
    ];
}
