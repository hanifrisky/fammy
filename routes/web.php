<?php

use App\Http\Controllers\PenilaianController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PenilaianController::class, 'index']);
Route::get('/bulanan', [PenilaianController::class, 'bulanan'])->name('bulanan');

Route::prefix('api')->group(function () {
    Route::get('/classes',  [PenilaianController::class, 'getClasses']);
    Route::get('/teachers', [PenilaianController::class, 'getTeachers']);
    Route::get('/students', [PenilaianController::class, 'getStudents']);
    Route::post('/submit',  [PenilaianController::class, 'submit']);
    Route::post('/submit-bulanan',  [PenilaianController::class, 'submitBulanan']);
});
