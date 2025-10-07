<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lectura_sensores;
use App\Services\AlarmaService;

class LecturaSensorController extends Controller
{

public function store(Request $req)
{
    // Validación mínima (ajusta a tu gusto)
    $data = $req->validate([
        'proceso_id'   => 'required|integer',
        'temperatura'  => 'required|numeric',
        'humedad'      => 'required|numeric',
        'fecha_hora'   => 'required|date',
        'error_motor'  => 'nullable|boolean',
        'error_foco'   => 'nullable|boolean',
        'error_sensor' => 'nullable|boolean',
    ]);

    $lectura = new Lectura_sensores($data);
    $lectura->error_motor  = $req->boolean('error_motor');
    $lectura->error_foco   = $req->boolean('error_foco');
    $lectura->error_sensor = $req->boolean('error_sensor');
    $lectura->save();

    // Evaluar/gestionar alertas
    AlarmaService::evaluarDespuesDeGuardarLectura($lectura);

    return response()->json(['ok'=>true]);
}

} 