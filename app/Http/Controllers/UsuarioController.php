<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Incubadora;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
{
    $usuarios = User::all(); // Puedes usar filtros si quieres excluir admin
    
       $incubadorasDisponibles = Incubadora::query()
        ->where('estado', 0)
        ->where(function ($q) {
            $q->whereNull('usuario_id')->orWhere('usuario_id', 0);
        })
        ->orderBy('codigo')
        ->get();

    return view('admin.usuarios.index', compact('usuarios','incubadorasDisponibles'));
}

public function create()
{
    
    return view('admin.usuarios.create')->with('usuarioGenerado', '');
}

public function store(Request $request)
{
    // Normaliza espacios en blanco
    // Normaliza campos
    $request->merge([
        'nombre'     => trim($request->nombre),
        'apellido1'  => trim($request->apellido1),
        'apellido2'  => trim((string)$request->apellido2),
        'telefono'   => preg_replace('/\s+/', '', (string)$request->telefono),
    ]);

    // ⚠️ MUY IMPORTANTE: si no hay correo, guardarlo como NULL (no como '')
    $correo = $request->filled('correo') ? strtolower(trim($request->correo)) : null;
    $request->merge(['correo' => $correo]);


    $request->validate([
        // Solo letras (unicode) y espacios: nombres y apellidos
        'nombre'     => ['required','string','max:60','regex:/^[\pL\s]+$/u'],
        'apellido1'  => ['required','string','max:60','regex:/^[\pL\s]+$/u'],
        'apellido2'  => ['nullable','string','max:60','regex:/^[\pL\s]+$/u'],

        // Correo válido y que termine en .com
        'correo'     => ['nullable','email:rfc','unique:usuarios,correo','ends_with:.com'],

        // CI/NIT (si lo quieres solo numérico, cambia la regla por el regex de abajo)
        //'ci_nit'     => ['required','string','max:15','unique:usuarios,ci_nit'],
         'ci_nit'  => ['required','regex:/^\d{8,12}$/','unique:usuarios,ci_nit'],

        // Teléfono: solo dígitos (7 a 15)
        'telefono'   => ['required','regex:/^\d{8,15}$/'],

        'direccion'  => ['nullable','string','max:150'],
        'rol'        => ['required','in:admin,avicultor,vendedor'],
    ], [
        'nombre.required'       => 'El Nombre es obligatorio.',
        'nombre.regex'          => 'El Nombre solo debe contener letras y espacios.',
        'apellido1.required'    => 'El Primer Apellido es obligatorio.',
        'apellido1.regex'       => 'El Primer Apellido solo debe contener letras y espacios.',
        'apellido2.regex'       => 'El Segundo Apellido solo debe contener letras y espacios.',
        'correo.email'          => 'El correo no tiene un formato válido.',
        'correo.ends_with'      => 'El correo debe terminar en .com.',
        'correo.unique'         => 'Este correo ya está registrado.',
        'ci_nit.required'       => 'El CI/NIT es obligatorio.',
        'ci_nit.unique'         => 'El CI/NIT ya está registrado.',
        'ci_nit.regex'        => 'El CI/NIT debe contener solo números (8 a 12 dígitos).',
        'telefono.required'     => 'El Teléfono es obligatorio.',
        'telefono.regex'        => 'El Teléfono debe contener solo números (8 a 15 dígitos).',
        'rol.required'          => 'Debes seleccionar un rol.',
        'rol.in'                => 'El rol seleccionado no es válido.',
    ]);


    // Generar nombre base: primera letra del nombre + primer apellido
    $base = strtoupper(substr($request->nombre, 0, 1)) . ucfirst(strtolower($request->apellido1));
    $usuarioGenerado = $base;
    $contador = 1;

    // Asegurar que no exista ya en la BD
    while (User::where('usuario', $usuarioGenerado)->exists()) {
        $usuarioGenerado = $base . $contador; 
        $contador++;
    }

    // Contraseña será igual al CI/NIT (encriptada)
    $contraseñaGenerada = Hash::make($request->ci_nit);

    $usuario = User::create([
        'nombre'         => $request->nombre,
        'apellido1'      => $request->apellido1,
        'apellido2'      => $request->apellido2,
        'usuario'        => $usuarioGenerado,
        'correo'         => $request->correo,
        'contraseña'     => $contraseñaGenerada,
        'rol'            => $request->rol,
        'ci_nit'         => $request->ci_nit,
        'telefono'       => $request->telefono,
        'direccion'      => $request->direccion,
        'modificado_por' => auth()->id(),
    ]);

    // === BLOQUE COMENTADO: ya NO preguntamos asignar incubadora aquí ===
    /*
    if ($usuario && $usuario->rol === 'avicultor') {
        return redirect()
            ->route('usuarios.index')
            ->with([
                'preguntar_incubadora' => true,
                'nuevo_usuario_id'     => $usuario->id,
                'nuevo_usuario_nombre' => $usuario->nombre.' '.$usuario->apellido1,
            ]);
    }
    */
    // ====================================================================

    return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
   {
      $usuario = User::findOrFail($id);
      return view('admin.usuarios.edit', compact('usuario'));
   }



    /**
     * Update the specified resource in storage.
     */
  public function update(Request $request, $id)
{
    $usuario = User::findOrFail($id);

    // Normaliza campos
    $request->merge([
        'nombre'     => trim($request->nombre),
        'apellido1'  => trim($request->apellido1),
        'apellido2'  => trim((string)$request->apellido2),
        'telefono'   => preg_replace('/\s+/', '', (string)$request->telefono),
    ]);

    $correo = $request->filled('correo') ? strtolower(trim($request->correo)) : null;
    $request->merge(['correo' => $correo]);



    $request->validate([
        'nombre'     => ['required','string','max:60','regex:/^[\pL\s]+$/u'],
        'apellido1'  => ['required','string','max:60','regex:/^[\pL\s]+$/u'],
        'apellido2'  => ['nullable','string','max:60','regex:/^[\pL\s]+$/u'],
        'correo'     => ['nullable','email:rfc',"unique:usuarios,correo,{$usuario->id}",'ends_with:.com'],
        'ci_nit'   => ['required','regex:/^\d{8,12}$/',"unique:usuarios,ci_nit,{$usuario->id}"],
        //'ci_nit'     => ['required','string','max:15',"unique:usuarios,ci_nit,{$usuario->id}"],
        'telefono'   => ['required','regex:/^\d{8,15}$/'],
        'direccion'  => ['nullable','string','max:150'],
        'rol'        => ['required','in:admin,avicultor,vendedor'],
        'estado'     => ['nullable','boolean'],
        'contraseña' => ['nullable','string','min:6','confirmed'],
    ], [
        'nombre.required'       => 'El Nombre es obligatorio.',
        'nombre.regex'          => 'El Nombre solo debe contener letras y espacios.',
        'apellido1.required'    => 'El Primer Apellido es obligatorio.',
        'apellido1.regex'       => 'El Primer Apellido solo debe contener letras y espacios.',
        'apellido2.regex'       => 'El Segundo Apellido solo debe contener letras y espacios.',
        'correo.email'          => 'El correo no tiene un formato válido.',
        'correo.ends_with'      => 'El correo debe terminar en .com.',
        'correo.unique'         => 'Este correo ya está registrado.',
        'ci_nit.required'       => 'El CI/NIT es obligatorio.',
        'ci_nit.unique'         => 'El CI/NIT ya está registrado.',
        'ci_nit.regex'        => 'El CI/NIT debe contener solo números (8 a 12 dígitos).',
        'telefono.required'     => 'El Teléfono es obligatorio.',
        'telefono.regex'        => 'El Teléfono debe contener solo números (8 a 15 dígitos).',
        'rol.required'          => 'Debes seleccionar un rol.',
        'rol.in'                => 'El rol seleccionado no es válido.',
        'contraseña.min'        => 'La contraseña debe tener al menos 6 caracteres.',
        'contraseña.confirmed'  => 'La confirmación de contraseña no coincide.',
    ]);

    // Generar base del usuario con nombre + apellido1
    $base = strtoupper(substr($request->nombre, 0, 1)) . ucfirst(strtolower($request->apellido1));
    $usuarioGenerado = $base;
    $contador = 1;

    // Evitar colisión con otros registros (excluye el propio id)
    while (
        User::where('usuario', $usuarioGenerado)
            ->where('id', '!=', $usuario->id)
            ->exists()
    ) {
        $usuarioGenerado = $base . $contador; // JPerez, JPerez1, JPerez2...
        $contador++;
    }

    // Contraseña nueva (si la ingresan) o basada en nuevo ci_nit
    $nuevaContraseña = $request->contraseña
        ? Hash::make($request->contraseña)
        : Hash::make($request->ci_nit);

    $usuario->update([
        'nombre'     => $request->nombre,
        'apellido1'  => $request->apellido1,
        'apellido2'  => $request->apellido2,
        'usuario'    => $usuarioGenerado,     // si quieres respetar un "usuario" manual, usa $request->usuario ?? $usuarioGenerado
        'correo'     => $request->correo,
        'ci_nit'     => $request->ci_nit,
        'telefono'   => $request->telefono,
        'direccion'  => $request->direccion,
        'rol'        => $request->rol,
        'estado'     => $request->estado ?? 1,
        'contraseña' => $nuevaContraseña,
        'modificado_por' => auth()->id(),
    ]);

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // Desactiva al usuario
        $usuario->update([
            'estado' => 0,
            'modificado_por' => auth()->id()
        ]);

        // Cuenta incubadoras activas del usuario
        $activas = Incubadora::where('usuario_id', $usuario->id)
            ->where('estado', 1)
            ->count();

        // Si no tiene incubadoras activas, salir normal
        if ($activas === 0) {
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario desactivado correctamente (no tenía incubadoras activas).');
        }

        // Si tiene incubadoras activas, preguntar si quieres desactivarlas
        return redirect()->route('usuarios.index')
            ->with('preguntar_baja_inc', true)
            ->with('usuario_baja_id', $usuario->id)
            ->with('usuario_baja_nombre', $usuario->nombre . ' ' . $usuario->apellido1)
            ->with('incubadoras_activas', $activas)
            ->with('success', 'Usuario desactivado correctamente.'); // opcional, para que también aparezca el toast
    }

    /**
     * Desactiva todas las incubadoras activas del usuario.
     */
    public function desactivarIncubadoras($id)
    {
        $usuario = User::findOrFail($id);

        Incubadora::where('usuario_id', $usuario->id)
            ->where('estado', 1)
            ->update([
                'estado' => 0,
                'modificado_por' => auth()->id(),
            ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Incubadoras del usuario desactivadas correctamente.');
    }
public function activar($id)
{
    $usuario = \App\Models\User::findOrFail($id);

    // Activa al usuario
    $usuario->update([
        'estado' => 1,
        'modificado_por' => auth()->id(),
    ]);

    // Cuenta incubadoras INACTIVAS del usuario
    $inactivas = Incubadora::where('usuario_id', $usuario->id)
        ->where('estado', 0)
        ->count();

    if ($inactivas > 0) {
        // Muestra modal para activar también sus incubadoras
        return redirect()->route('usuarios.index')
            ->with('preguntar_alta_inc', true)
            ->with('usuario_alta_id', $usuario->id)
            ->with('usuario_alta_nombre', $usuario->nombre.' '.$usuario->apellido1)
            ->with('incubadoras_inactivas', $inactivas)
            ->with('success', 'Usuario activado correctamente.');
    }

    return redirect()->route('usuarios.index')
        ->with('success', 'Usuario activado correctamente (no tenía incubadoras inactivas).');
}

/** Activa todas las incubadoras inactivas del usuario */
public function activarIncubadoras($id)
{
    $usuario = \App\Models\User::findOrFail($id);

    Incubadora::where('usuario_id', $usuario->id)
        ->where('estado', 0)
        ->update([
            'estado' => 1,
            'modificado_por' => auth()->id(),
        ]);

    return redirect()->route('usuarios.index')
        ->with('success', 'Incubadoras del usuario activadas correctamente.');
}

// Sugerir nombre de usuario en vivo (para el formulario de crear/editar)
public function sugerirUsuario(Request $request)
{
    // Validar que lleguen nombre y primer apellido
    $request->validate([
        'nombre'    => ['required','string'],
        'apellido1' => ['required','string'],
        // Para edición puedes enviar excluir_id y no colisionar contigo mismo
        'excluir_id'=> ['nullable','integer'],
    ]);

    // Mismo algoritmo que usas en store/update:
    // primera letra del nombre (MAYÚS) + primer apellido (Capitalizado)
    $base = strtoupper(substr($request->nombre, 0, 1)) . ucfirst(strtolower($request->apellido1));
    $usuarioGenerado = $base;
    $contador = 1;

    // Unicidad (si mandas excluir_id, no choca con ese registro)
    while (
        \App\Models\User::where('usuario', $usuarioGenerado)
            ->when($request->filled('excluir_id'), fn($q) => $q->where('id', '!=', $request->excluir_id))
            ->exists()
    ) {
        $usuarioGenerado = $base . $contador; // JPerez, JPerez1, JPerez2...
        $contador++;
    }

    return response()->json(['usuario' => $usuarioGenerado]);
}
public function formAsignarIncubadora(User $usuario)
{
    // Incubadoras “disponibles” = sin dueño: estado = 0 y usuario_id NULL/0
    $incubadorasDisponibles = Incubadora::query()
        ->where('estado', 0)
        ->where(function ($q) {
            $q->whereNull('usuario_id')->orWhere('usuario_id', 0);
        })
        ->orderBy('codigo')
        ->get();

    return view('admin.usuarios.asignar-incubadora', compact('usuario', 'incubadorasDisponibles'));
}

public function asignarIncubadora(Request $request, User $usuario)
{
    $request->validate([
        'incubadora_id' => ['required','integer','exists:incubadoras,id'],
    ], [
        'incubadora_id.required' => 'Debes seleccionar una incubadora.',
    ]);

    // Solo permite asignar si sigue “libre”
    $inc = Incubadora::query()
        ->where('id', $request->incubadora_id)
        ->where('estado', 0)
        ->where(function ($q) {
            $q->whereNull('usuario_id')->orWhere('usuario_id', 0);
        })
        ->firstOrFail();

    $inc->usuario_id     = $usuario->id;
    $inc->estado         = 1;                 // activa al asignar
    $inc->modificado_por = auth()->id();
    $inc->save();

    return redirect()
        ->route('admin.usuarios')
        ->with('success', "Incubadora {$inc->codigo} asignada a {$usuario->nombre} {$usuario->apellido1}.");
}


}
