<?php

use Illuminate\Support\Facades\Route;
use Mafrasil\LaravelSonar\Http\Controllers\SonarDashboardController;

if (config('sonar.dashboard.enabled', true)) {
    Route::prefix(config('sonar.dashboard.path', 'sonar'))
        ->middleware(['web'])
        ->group(function () {
            Route::get('/', [SonarDashboardController::class, 'index'])->name('sonar.dashboard');
            Route::get('/elements/{elementName}', [SonarDashboardController::class, 'show'])->name('sonar.element.show');
        });
}
