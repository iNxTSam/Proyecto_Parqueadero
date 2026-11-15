<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\VigilanteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'show'])->name('login');
Route::post('/logging', [LoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Vigilante
Route::middleware(['auth', 'role:3', 'no-cache'])->prefix('vigilante')->name('vigilante.')->group(function () {
    Route::get('/', [VigilanteController::class, 'dashboard'])->name('dashboard');
    Route::get('/entradas-salidas', [VigilanteController::class, 'entradasSalidas'])->name('entradas');
    Route::post('/entradas-salidas', [VigilanteController::class, 'registrarIngreso'])->name('entradas.store');
    Route::put('/entradas-salidas/{entrada}/salida', [VigilanteController::class, 'registrarSalida'])->name('entradas.salida');
    Route::post('/verificar-cliente', [VigilanteController::class, 'verificarCliente'])->name('verificarCliente');
    Route::post('/verificar-contrato', [VigilanteController::class, 'verificarContrato'])->name('verificarContrato');
    Route::get('/vehiculos', [VigilanteController::class, 'vehiculos'])->name('vehiculos');
});

// Administrador
Route::middleware(['auth', 'role:2', 'no-cache'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios');
    Route::post('/usuarios', [AdminController::class, 'storeUsuario'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [AdminController::class, 'updateUsuario'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/toggle', [AdminController::class, 'toggleUsuario'])->name('usuarios.toggle');

    Route::get('/contratos', [AdminController::class, 'contratos'])->name('contratos');
    Route::post('/contratos', [AdminController::class, 'storeContrato'])->name('contratos.store');
    Route::put('/contratos/{contrato}', [AdminController::class, 'updateContrato'])->name('contratos.update');
    Route::post('/contratos/{contrato}/renovar', [AdminController::class, 'renovarContrato'])->name('contratos.renovar');
    Route::post('/contratos/verificar-usuario', [AdminController::class, 'verificarUsuario'])->name('contratos.verificarUsuario');

    Route::get('/clientes', [AdminController::class, 'clientes'])->name('clientes');
    Route::post('/clientes', [AdminController::class, 'storeCliente'])->name('clientes.store');
    Route::put('/clientes/{cliente}', [AdminController::class, 'updateCliente'])->name('clientes.update');

    Route::get('/vehiculos', [AdminController::class, 'vehiculos'])->name('vehiculos');
    Route::post('/vehiculos', [AdminController::class, 'storeVehiculo'])->name('vehiculos.store');
    Route::put('/vehiculos/{vehiculo}', [AdminController::class, 'updateVehiculo'])->name('vehiculos.update');
});

// Superadministrador
Route::middleware(['auth', 'role:1', 'no-cache'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/tarifas', [SuperAdminController::class, 'tarifas'])->name('tarifas');
    Route::post('/tarifas', [SuperAdminController::class, 'storeTarifa'])->name('tarifas.store');
    Route::put('/tarifas/{tarifa}', [SuperAdminController::class, 'updateTarifa'])->name('tarifas.update');
    Route::delete('/tarifas/{tarifa}', [SuperAdminController::class, 'deleteTarifa'])->name('tarifas.delete');

    Route::get('/bahias', [SuperAdminController::class, 'bahias'])->name('bahias');
    Route::post('/bahias', [SuperAdminController::class, 'storeBahia'])->name('bahias.store');
    Route::put('/bahias/{bahia}', [SuperAdminController::class, 'updateBahia'])->name('bahias.update');
    Route::delete('/bahias/{bahia}', [SuperAdminController::class, 'deleteBahia'])->name('bahias.delete');

    Route::get('/reportes', [SuperAdminController::class, 'reportes'])->name('reportes');
    Route::post('/reportes/generar', [SuperAdminController::class, 'generarReporte'])->name('reportes.generar');

    Route::get('/configuracion', [SuperAdminController::class, 'configuracion'])->name('configuracion');
    Route::post('/configuracion/tipo-vehiculo', [SuperAdminController::class, 'storeTipoVehiculo'])->name('configuracion.tipoVehiculo.store');
    Route::put('/configuracion/tipo-vehiculo/{tipoVehiculo}', [SuperAdminController::class, 'updateTipoVehiculo'])->name('configuracion.tipoVehiculo.update');
    Route::delete('/configuracion/tipo-vehiculo/{tipoVehiculo}', [SuperAdminController::class, 'deleteTipoVehiculo'])->name('configuracion.tipoVehiculo.delete');
});
