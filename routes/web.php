<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/employees/concept-report', [EmployeeReportController::class, 'conceptReport'])->name('employees.concept-report');
