<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProyectoController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('clientes', function () {
    return Inertia::render('Cliente');
})->middleware(['auth', 'verified'])->name('clientes');

Route::get('proyectos', function () {
    return Inertia::render('Proyecto');
})->middleware(['auth', 'verified'])->name('proyectos');

// API Routes para Clientes
Route::middleware(['auth'])->group(function () {
    Route::prefix('clientes')->group(function () {
        Route::get('/listar', [ClienteController::class, 'listarCliente']);
        Route::post('/guardar', [ClienteController::class, 'guardarCliente']);
        Route::put('/editar/{id}', [ClienteController::class, 'editarCliente']);
        Route::delete('/eliminar/{id}', [ClienteController::class, 'eliminarCliente']);
        Route::get('/exportar-pdf', [ClienteController::class, 'exportarPdfCliente']);
    });
});

// API Routes para Proyectos - ¡ESTAS FALTAN!
Route::middleware(['auth'])->group(function () {
    Route::prefix('proyectos')->group(function () {
        Route::get('/listar', [ProyectoController::class, 'listarProyecto']);
        Route::post('/guardar', [ProyectoController::class, 'guardarProyecto']);
        Route::put('/editar/{id}', [ProyectoController::class, 'editarProyecto']);
        Route::delete('/eliminar/{id}', [ProyectoController::class, 'eliminarProyecto']);
        Route::get('/exportar-pdf', [ProyectoController::class, 'exportarPdfProyecto']);
    });
});

require __DIR__.'/settings.php';