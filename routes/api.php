<?php

use App\Http\Controllers\Api\SCORMTrackingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:scorm'])->group(function () {
    Route::post('/scorm/{package:uuid}/track', [SCORMTrackingController::class, 'store'])->name('api.scorm.track');
});
