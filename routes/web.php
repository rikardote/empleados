<?php

use App\Http\Controllers\PdfFillingController;
use App\Http\Controllers\PdfInspectorController;
use App\Http\Controllers\EmployeeReportController;
use App\Http\Controllers\Fm1ImportController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/pdf/fill/{id?}', [PdfFillingController::class, 'showForm'])->name('pdf.fill');
Route::get('/pdf/fill-visual/{id?}', [PdfFillingController::class, 'showVisualForm'])->name('pdf.fill-visual');
Route::post('/pdf/fill', [PdfFillingController::class, 'fill'])->name('pdf.process');
Route::post('/pdf/update/{id}', [PdfFillingController::class, 'update'])->name('pdf.update');
Route::delete('/pdf/delete/{id}', [PdfFillingController::class, 'destroy'])->name('pdf.destroy');

// Inspector de coordenadas PDF
Route::get('/pdf/inspector', [PdfInspectorController::class, 'index'])->name('pdf.inspector');
Route::get('/pdf/inspector/preview', [PdfInspectorController::class, 'previewPdf'])->name('pdf.inspector.preview');
Route::get('/pdf/inspector/coords', [PdfInspectorController::class, 'getCoordinates'])->name('pdf.inspector.coords');
Route::post('/pdf/inspector/save', [PdfInspectorController::class, 'saveCoordinates'])->name('pdf.inspector.save');

Route::get('/employees/concept-report', [EmployeeReportController::class, 'conceptReport'])->name('employees.concept-report');
Route::get('/employees/concept-report/export', [EmployeeReportController::class, 'exportExcel'])->name('employees.concept-report.export');
Route::get('/employees/compare-periods', [EmployeeReportController::class, 'comparePeriods'])->name('employees.compare-periods');
Route::get('/employees/download-import/{filename}', [EmployeeReportController::class, 'downloadImport'])->name('employees.download-import');
Route::delete('/employees/delete-import/{filename}', [EmployeeReportController::class, 'deleteImport'])->name('employees.delete-import');

Route::get('/employees/import', [EmployeeReportController::class, 'importForm'])->name('employees.import-form');
Route::post('/employees/import', [EmployeeReportController::class, 'import'])->name('employees.import');
Route::get('/employees/search', [EmployeeReportController::class, 'search'])->name('employees.search');

// FM1 Import via Excel
Route::get('/fm1/import', [Fm1ImportController::class, 'index'])->name('fm1.import.index');
Route::get('/fm1/import/template', [Fm1ImportController::class, 'downloadTemplate'])->name('fm1.import.template');
Route::post('/fm1/import', [Fm1ImportController::class, 'store'])->name('fm1.import.store');
Route::get('/fm1/import/batch/{batchId}', [Fm1ImportController::class, 'showBatch'])->name('fm1.import.batch');
Route::get('/fm1/import/batch/{batchId}/download', [Fm1ImportController::class, 'downloadBatch'])->name('fm1.import.batch.download');
Route::delete('/fm1/import/batch/{batchId}', [Fm1ImportController::class, 'destroyBatch'])->name('fm1.import.batch.destroy');
Route::get('/fm1/import/record/{id}/pdf', [Fm1ImportController::class, 'downloadOne'])->name('fm1.import.record.pdf');
