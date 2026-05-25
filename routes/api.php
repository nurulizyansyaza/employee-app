<?php

use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\OcrController;
use Illuminate\Support\Facades\Route;

Route::post('ocr/plate', [OcrController::class, 'plate'])
    ->middleware('throttle:5,1')
    ->name('api.ocr.plate');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('employees/ocr/plate', [OcrController::class, 'plate'])
        ->middleware('throttle:5,1')
        ->name('api.employees.ocr.plate');

    Route::apiResource('employees', EmployeeController::class)->names('api.employees');
});
