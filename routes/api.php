<?php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('employees/periods', [EmployeeController::class, 'getPeriods']);
Route::get('employees/search', [EmployeeController::class, 'search']);
Route::get('employees/import-status/{id}', [EmployeeController::class, 'getImportStatus']);
Route::post('employees/import', [EmployeeController::class, 'import']);
Route::apiResource('employees', EmployeeController::class);
