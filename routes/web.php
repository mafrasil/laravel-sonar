<?php

use Illuminate\Support\Facades\Route;
use Mafrasil\LaravelSonar\Http\Controllers\SonarDashboardController;

Route::prefix(config('sonar.path', 'sonar'))->group(function () {
    Route::get('/', [SonarDashboardController::class, 'index'])->name('sonar.dashboard');
    Route::get('/elements/{elementName}', [SonarDashboardController::class, 'show'])->name('sonar.element.show');
});
