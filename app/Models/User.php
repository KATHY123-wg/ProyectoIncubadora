<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    use HasFactory;

    // App/Models/User.php

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = $this->toUpperClean($value);
    }

    public function setApellido1Attribute($value)
    {
        $this->attributes['apellido1'] = $this->toUpperClean($value);
    }

    public function setApellido2Attribute($value)
    {
        // si apellido2 es nullable en tu BD, respeta null
        $this->attributes['apellido2'] = $this->toUpperClean($value, allowNull: true);
    }

    /**
     * Normaliza: trim, colapsa espacios y convierte a MAYÚSCULAS (UTF-8).
     */
    private function toUpperClean($value, bool $allowNull = false)
    {
        if ($allowNull && ($value === null || $value === '')) {
            return null;
        }
        $v = trim((string)$value);
        // Un solo espacio entre palabras
        $v = preg_replace('/\s+/u', ' ', $v);
        return mb_strtoupper($v, 'UTF-8');
    }
    public function setCorreoAttribute($value)
    {
        if ($value === null) {
            $this->attributes['correo'] = null;
            return;
        }
        $v = trim((string)$value);
        $this->attributes['correo'] = $v === '' ? null : strtolower($v);
    }


    protected $table = 'usuarios';


    protected $fillable = [
        'nombre',
        'apellido1',
        'apellido2',
        'usuario',
        'correo',
        'contraseña',
        'ci_nit',
        'telefono',
        'direccion',
        'rol',
        'estado',
        'modificado_por'
    ];

    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->contraseña;
    }
    public function incubadoras()
    {
        return $this->hasMany(Incubadora::class, 'usuario_id');
    }
}
