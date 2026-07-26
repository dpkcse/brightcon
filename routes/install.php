<?php

use App\Http\Controllers\InstallerController;
use Illuminate\Support\Facades\Route;

Route::prefix('install')->name('install.')->middleware(['install.open', 'throttle:20,1'])->group(function (): void {
    Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('requirements');
    Route::get('/permissions', [InstallerController::class, 'permissions'])->name('permissions');
    Route::get('/database', [InstallerController::class, 'form'])->name('database');
    Route::get('/application', [InstallerController::class, 'form'])->name('application');
    Route::get('/admin', [InstallerController::class, 'form'])->name('admin');
    Route::get('/data-mode', [InstallerController::class, 'form'])->name('data');
    Route::post('/review', [InstallerController::class, 'review'])->name('review');
    Route::post('/summary', [InstallerController::class, 'review'])->name('summary');
    Route::get('/processing', fn () => redirect()->route('install.application'))->name('processing');
    Route::post('/run', [InstallerController::class, 'process'])->middleware('throttle:3,10')->name('process');
    Route::get('/complete', [InstallerController::class, 'complete'])->name('complete');
});
