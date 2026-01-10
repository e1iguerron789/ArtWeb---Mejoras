<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserInteresesController;
use App\Http\Controllers\Admin\CategoriaInteresController;
use App\Http\Controllers\Admin\OpcionInteresController;
use Inertia\Inertia;
use App\Http\Controllers\EventoController;


// -----------------------------------
// Página de inicio = Login
// -----------------------------------
Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'   => PHP_VERSION,
    ]);
});

// -----------------------------------
// Dashboard según rol
// -----------------------------------
Route::get('/dashboard', function () {

    if (auth()->user()->rol === 'admin') {
        return redirect()->route('admin.dashboard');
    }

  return redirect()->route('eventos.mapa');



})->middleware(['auth', 'verified'])->name('dashboard');


// -----------------------------------
// RUTAS DEL USUARIO (AUTH)
// -----------------------------------
Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::get('/intereses', [UserInteresesController::class, 'index'])->name('intereses.index');
    Route::get('/intereses/create', [UserInteresesController::class, 'create'])->name('intereses.create');
    Route::post('/intereses', [UserInteresesController::class, 'store'])->name('intereses.store');

    
    Route::get('/intereses/{userIntereses}/edit', [UserInteresesController::class, 'edit'])->name('intereses.edit');
    Route::patch('/intereses/{userIntereses}', [UserInteresesController::class, 'update'])->name('intereses.update');
    Route::delete('/intereses/{userIntereses}', [UserInteresesController::class, 'destroy'])->name('intereses.destroy');


    // Eliminar todos
    Route::delete('/intereses/delete-all', [UserInteresesController::class, 'destroyAll'])
        ->name('intereses.destroyAll');

    //Eventos
    Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
    Route::get('/eventos/create', [EventoController::class, 'create'])->name('eventos.create');
    Route::post('/eventos', [EventoController::class, 'store'])->name('eventos.store');
    
    //Mapa
    Route::get('/mapa', [EventoController::class, 'mapa'])->name('eventos.mapa');
     
   Route::post('/eventos/mapa/actualizar', [EventoController::class, 'actualizarMapa'])
    ->name('eventos.mapa.post');


});


// -----------------------------------
// RUTAS DEL ADMIN
// -----------------------------------
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return inertia('Admin/CategoriasInteres/Index');
        })->name('dashboard');

        // CRUD Categorías
        Route::resource('categorias-interes', CategoriaInteresController::class);

        // CRUD Opciones
        Route::resource('opciones-interes', OpcionInteresController::class);
    });


// -----------------------------------
// API para cargar opciones dinámicamente
// -----------------------------------
Route::get('/api/opciones/{categoriaId}', function ($categoriaId) {
    return \App\Models\OpcionInteres::where('categoria_interes_id', $categoriaId)->get();
});

require __DIR__ . '/auth.php';
