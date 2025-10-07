<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AvicultorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UsuarioController;
use App\Models\Incubadora;
use App\Livewire\Admin\IncubadorasAdmin;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ReportesPVExportController;
use App\Http\Controllers\AvicultorMetricsController;




Route::get('/', function () {
    return redirect()->route('login');
});

// Accesos públicos
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {

    // Rutas para admin
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/inicio', [AdminController::class, 'inicio'])->name('inicio');
        Route::get('/reportes', [AdminController::class, 'historial'])->name('reportes');
        Route::get('/nosotros', function () {
            return view('admin.nosotros');
        })->name('nosotros');
        Route::get('/ventas', [AdminController::class, 'ventas'])->name('ventas');
        Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    });

    Route::middleware(['auth', 'role:avicultor'])->prefix('avicultor')->group(function () {
        Route::get('/inicio', [AvicultorController::class, 'inicio'])->name('avicultor.inicio');
        Route::get('/graficos', [AvicultorController::class, 'graficos'])->name('avicultor.graficos');
        Route::get('/reportes', [AvicultorController::class, 'historial'])->name('avicultor.reportes');
        Route::get('/procesos', [AvicultorController::class, 'ciclos'])->name('avicultor.procesos');
        Route::get('/nosotros', [AvicultorController::class, 'nosotros'])->name('avicultor.nosotros');
        Route::get('/lecturas-por-incubadora/{incubadoraId}', [AvicultorController::class, 'lecturasPorIncubadora']);
        // routes/web.php (dentro del middleware 'auth' + 'role:avicultor')
      //  Route::get('/metrics', [AvicultorController::class, 'metrics'])->name('avicultor.metrics');

    });

    // Rutas para vendedor
    Route::middleware('role:vendedor')->prefix('vendedor')->group(function () {
        Route::get('/inicio', [VentaController::class, 'inicio'])->name('vendedor.inicio');
        Route::get('/ventas', [VentaController::class, 'index'])->name('vendedor.ventas');
        Route::get('/nosotros', [VentaController::class, 'nosotros'])->name('vendedor.nosotros');
    });

    ////////////// area de la incubadora
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/graficos', function () {
        return view('admin.graficos');
    })->name('admin.graficos');
});

Route::get('/admin/lecturas-incubadora/{id}', [AdminController::class, 'lecturasPorIncubadora']);
Route::get('/admin/errores-por-incubadora/{incubadoraId}', [AdminController::class, 'obtenerErrores']);

//////////////area de usuarios/administrador
Route::resource('usuarios', UsuarioController::class);

Route::middleware(['auth'])->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/admin/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios');
        Route::get('/admin/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/admin/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/admin/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/admin/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/admin/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });
});

////////////////area de reportes para admin
//Route::get('/admin/reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::get('/admin/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
Route::get('/admin/reportes/procesos', [ReporteController::class, 'procesos'])->name('reportes.procesos');
Route::get('/api/incubadoras-por-usuario/{usuario_id}', [ReporteController::class, 'getIncubadoras']);
Route::get('/api/proceso-incubadora/{incubadora_id}/{gestion}', [ReporteController::class, 'getProceso']);

/////////////////////midleware admin/usuarios
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/incubadoras', fn () => view('admin.incubadoras.index'))
        ->name('admin.incubadoras');
});

Route::post('/usuarios/{id}/desactivar-incubadoras', [\App\Http\Controllers\UsuarioController::class, 'desactivarIncubadoras'])
    ->name('usuarios.desactivar-incubadoras')
    ->middleware(['auth']); // añade tu middleware de rol si corresponde

Route::post('/usuarios/{id}/activar', [\App\Http\Controllers\UsuarioController::class, 'activar'])
    ->name('usuarios.activar')
    ->middleware(['auth']); // agrega tu middleware de rol si corresponde

Route::post('/usuarios/{id}/activar-incubadoras', [\App\Http\Controllers\UsuarioController::class, 'activarIncubadoras'])
    ->name('usuarios.activar-incubadoras')
    ->middleware(['auth']); // agrega middleware de rol si corresponde

////////////
Route::get('/usuarios/sugerir', [UsuarioController::class, 'sugerirUsuario'])
    ->name('usuarios.sugerir');

/////////////
// routes/web.php

/* 🔕 INHABILITADO: endpoints de asignación directa de incubadoras (se hará vía módulo de Ventas)
Route::post('/admin/incubadoras/asignar', [AdminController::class, 'asignarIncubadora'])
    ->name('admin.incubadoras.asignar')
    ->middleware(['auth','role:admin']);
*/

 /* 🔕 INHABILITADO: formulario y acción para asignar incubadora a usuario desde Usuarios
Route::middleware(['auth','role:admin'])->group(function () {
    // Formulario para elegir incubadora y asignarla al usuario
    Route::get('/admin/usuarios/{usuario}/asignar-incubadora', [UsuarioController::class, 'formAsignarIncubadora'])
        ->name('usuarios.form-asignar-incubadora');

    // Procesa la asignación
    Route::post('/admin/usuarios/{usuario}/asignar-incubadora', [UsuarioController::class, 'asignarIncubadora'])
        ->name('usuarios.asignar-incubadora');
});
*/

/* 🔕 INHABILITADO: ruta duplicada de asignación (queda comentada para evitar confusión)
Route::middleware(['auth','role:admin'])->group(function () {
    Route::post('/admin/usuarios/{usuario}/asignar-incubadora', [UsuarioController::class, 'asignarIncubadora'])
        ->name('usuarios.asignar-incubadora');
});
*/
Route::get('/ventas/{venta}/recibo', [VentaController::class, 'recibo'])->name('ventas.recibo');
///


Route::middleware(['auth'])->group(function () {
    // Usuarios
    Route::get('/reportes/usuarios/pdf', [ExportController::class, 'usuariosPDF'])->name('reportes.usuarios.pdf');
    Route::get('/reportes/usuarios/csv', [ExportController::class, 'usuariosCSV'])->name('reportes.usuarios.csv');
    Route::get('/reportes/usuarios/xls', [ExportController::class, 'usuariosXLS'])->name('reportes.usuarios.xls');

    // Incubadoras
    Route::get('/reportes/incubadoras/pdf', [ExportController::class, 'incubadorasPDF'])->name('reportes.incubadoras.pdf');
    Route::get('/reportes/incubadoras/csv', [ExportController::class, 'incubadorasCSV'])->name('reportes.incubadoras.csv');
    Route::get('/reportes/incubadoras/xls', [ExportController::class, 'incubadorasXLS'])->name('reportes.incubadoras.xls');
});

Route::get('/ping', fn () => 'ok');

//////////////////////[reportes ventas y procesos]

Route::middleware(['auth'])->group(function () {
    // PROCESOS
    Route::get('/admin/reportes/procesos/export/pdf', [ReportesPVExportController::class, 'procesosPDF'])
        ->name('reportes.procesos.pdf');
    Route::get('/admin/reportes/procesos/export/xls', [ReportesPVExportController::class, 'procesosXLS'])
        ->name('reportes.procesos.xls');

    // VENTAS
    Route::get('/admin/reportes/ventas/export/pdf', [ReportesPVExportController::class, 'ventasPDF'])
        ->name('reportes.ventas.pdf');
    Route::get('/admin/reportes/ventas/export/xls', [ReportesPVExportController::class, 'ventasXLS'])
        ->name('reportes.ventas.xls');
});
Route::middleware(['auth','role:avicultor'])->group(function () {
    Route::get('/avicultor/reportes/procesos/pdf', [ReportesPVExportController::class, 'avicultorProcesosPDF'])
        ->name('avicultor.procesos.pdf');

    Route::get('/avicultor/reportes/procesos/xls', [ReportesPVExportController::class, 'avicultorProcesosXLS'])
        ->name('avicultor.procesos.xls');
});

////////////alertas

Route::middleware(['auth'])->group(function () {
    Route::get('/alertas', function () {
        $user = auth()->user();
        if (!in_array($user->rol, ['admin','avicultor'])) {
            abort(403); // vendedor no entra
        }
        return view('alertas.alertas');
    })->name('alertas.panel');
});
//////////// lectura para el rol avicultor
Route::middleware(['auth'])->group(function () {
   Route::get('/avicultor/metrics', AvicultorMetricsController::class)
       ->name('avicultor.metrics');
});
