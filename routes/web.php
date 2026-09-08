<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BocaminaController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\TrabajoController;
use App\Http\Controllers\AnticipoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TransaccionMineralController;
use App\Http\Controllers\TipoTrabajoController;
use App\Http\Controllers\ServicioExternoController;

// Public routes / Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Protected routes (Only logged-in admin users)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', [ReporteController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', function() {
        return redirect()->route('dashboard');
    });

    // Bocaminas
    Route::resource('bocaminas', BocaminaController::class)->only(['index', 'store', 'update', 'destroy']);

    // Trabajadores
    Route::resource('trabajadores', TrabajadorController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['trabajadores' => 'trabajador']);

    // Tipos de Trabajo
    Route::resource('tipos-trabajo', TipoTrabajoController::class);

    // Servicios Externos
    Route::get('/servicios-externos/{servicio}/recibo', [ServicioExternoController::class, 'recibo'])->name('servicios-externos.recibo');
    Route::resource('servicios-externos', ServicioExternoController::class);



    // Registro de Trabajos
    // Route::resource('trabajos', TrabajoController::class)->only(['index', 'store', 'update', 'destroy']);

    // Anticipos
    Route::get('/anticipos/{anticipo}/recibo', [App\Http\Controllers\AnticipoController::class, 'recibo'])->name('anticipos.recibo');
    Route::resource('anticipos', AnticipoController::class)->only(['index', 'store', 'update', 'destroy']);

    // Pagos
    Route::get('/pagos', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/pagos/crear', [PagoController::class, 'create'])->name('pagos.create');
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');
    Route::get('/pagos/trabajador-data/{id}', [PagoController::class, 'getTrabajadorData'])->name('pagos.trabajador-data');
    Route::get('/pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
    Route::get('/pagos/{pago}/editar', [PagoController::class, 'edit'])->name('pagos.edit');
    Route::put('/pagos/{pago}', [PagoController::class, 'update'])->name('pagos.update');
    Route::delete('/pagos/{pago}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    Route::get('/fondos-caja', [PagoController::class, 'fondosIndex'])->name('fondos-caja.index');
    Route::post('/fondos-pagos', [PagoController::class, 'storeFondo'])->name('fondos-pagos.store');
    Route::delete('/fondos-pagos/{id}', [PagoController::class, 'destroyFondo'])->name('fondos-pagos.destroy');
    Route::post('/fondos-caja/reiniciar', [PagoController::class, 'reiniciarCaja'])->name('fondos-caja.reiniciar');
    Route::delete('/fondos-caja/limpiar-anteriores', [PagoController::class, 'limpiarAnteriores'])->name('fondos-caja.limpiar-anteriores');

    // Compra y Venta de Concentrados/Sacos & Caja del Módulo 2
    Route::get('/transacciones-minerales/{id}/ticket', [TransaccionMineralController::class, 'ticket'])->name('transacciones-minerales.ticket');
    Route::post('/caja-minerales/recarga', [TransaccionMineralController::class, 'storeRecarga'])->name('caja-minerales.store-recarga');
    Route::delete('/caja-minerales/recarga/{id}', [TransaccionMineralController::class, 'destroyRecarga'])->name('caja-minerales.destroy-recarga');
    Route::resource('transacciones-minerales', TransaccionMineralController::class);

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes-comercializacion', [ReporteController::class, 'comercializacion'])->name('reportes-comercializacion.index');

    // Copias de Seguridad (Backups)
    Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [App\Http\Controllers\BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/descargar/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/restaurar', [App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');
    Route::post('/backups/configurar', [App\Http\Controllers\BackupController::class, 'saveSettings'])->name('backups.configurar');
    Route::delete('/backups/eliminar/{filename}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');

    // Perfil de Usuario
    Route::get('/perfil', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/perfil', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
