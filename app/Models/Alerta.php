<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Alerta extends Model
{
    protected $table = 'alertas';

    // Usamos timestamps personalizados como en tus tablas
    public const CREATED_AT = 'fecha_registro';
    public const UPDATED_AT = 'ultima_actualizacion';

    protected $fillable = [
        'incubadora_id','proceso_id','tipo','codigo','nivel','mensaje',
        'valor_actual','umbral','estado','ocurrencias','silenciada_hasta',
        'resuelta_en','resuelta_por',
    ];

    protected $casts = [
        'silenciada_hasta' => 'datetime',
        'resuelta_en'      => 'datetime',
    ];

    // Relaciones
    public function incubadora() { return $this->belongsTo(Incubadora::class); }
    public function proceso()    { return $this->belongsTo(Incubacion::class); }
    public function usuarioResuelve(){ return $this->belongsTo(User::class, 'resuelta_por'); }

    // Scopes de conveniencia
    public function scopeAbiertas(Builder $q){ return $q->where('estado','abierta'); }
    public function scopeActivas(Builder $q){
        return $q->where('estado','abierta')
                 ->where(function($qq){
                     $qq->whereNull('silenciada_hasta')->orWhere('silenciada_hasta','<', now());
                 });
    }
}
