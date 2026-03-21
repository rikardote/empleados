<?php

use App\Http\Controllers\PdfFillingController;
use App\Http\Controllers\EmployeeReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pdf/fill/{id?}', [PdfFillingController::class, 'showForm'])->name('pdf.fill');
Route::post('/pdf/fill', [PdfFillingController::class, 'fill'])->name('pdf.process');
Route::post('/pdf/update/{id}', [PdfFillingController::class, 'update'])->name('pdf.update');
Route::delete('/pdf/delete/{id}', [PdfFillingController::class, 'destroy'])->name('pdf.destroy');

Route::get('/employees/concept-report', [EmployeeReportController::class, 'conceptReport'])->name('employees.concept-report');
Route::get('/employees/concept-report/export', [EmployeeReportController::class, 'exportExcel'])->name('employees.concept-report.export');
Route::get('/employees/compare-periods', [EmployeeReportController::class, 'comparePeriods'])->name('employees.compare-periods');
Route::get('/employees/download-import/{filename}', [EmployeeReportController::class, 'downloadImport'])->name('employees.download-import');
Route::delete('/employees/delete-import/{filename}', [EmployeeReportController::class, 'deleteImport'])->name('employees.delete-import');

Route::get('/employees/import', [EmployeeReportController::class, 'importForm'])->name('employees.import-form');
Route::post('/employees/import', [EmployeeReportController::class, 'import'])->name('employees.import');
Route::get('/employees/search', [EmployeeReportController::class, 'search'])->name('employees.search');
