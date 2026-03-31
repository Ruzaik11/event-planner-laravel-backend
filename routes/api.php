<?php

use App\Http\Controllers\EventPlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('event-plans')->group(function () {
    Route::post('/', [EventPlanController::class, 'generate']);
    Route::get('/image', [EventPlanController::class, 'searchImage']);
});