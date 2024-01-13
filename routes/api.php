<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    // Ruta existente para obtener los detalles del usuario
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Nuevas rutas protegidas por Sanctum y verificación de usuario
    Route::prefix('profile')->group(function () {
        Route::put('/update', [ProfileController::class, 'updateUser'])->name('profile.update');
        Route::put('/change-password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
        Route::delete('/delete-account', [ProfileController::class, 'deleteAccount'])->name('profile.delete-account');
    });

    // Otras rutas protegidas por Sanctum y verificación de usuario
    // ...
});
