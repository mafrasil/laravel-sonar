<?php

use Illuminate\Support\Facades\Route;
use Mafrasil\LaravelSonar\Http\Controllers\SonarController;

Route::prefix('api')->group(function () {
    Route::post('/sonar/events', [SonarController::class, 'store'])
        ->name('sonar.events.store');
});
