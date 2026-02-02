<?php

use Illuminate\Support\Facades\Route;

// Redirigir la raíz al panel de Filament
Route::get('/', function () {
    return redirect('/admin');
});
