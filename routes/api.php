<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\HealthUnitController;
use App\Http\Controllers\Api\PatientController;

Route::get('/health-units', [HealthUnitController::class, 'index']);

Route::get('/patients', [PatientController::class, 'index']);
Route::post('/patients', [PatientController::class, 'store']);
Route::get('/patients/{patient}', [PatientController::class, 'show']);
Route::put('/patients/{patient}', [PatientController::class, 'update']);
Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);