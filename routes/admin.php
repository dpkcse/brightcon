<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterLinkController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\Settings\GeneralSettingsController;
use App\Http\Controllers\Admin\Settings\ThemeSettingsController;
use App\Http\Controllers\Admin\SocialLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth')->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings/general', [GeneralSettingsController::class, 'edit'])->name('settings.general.edit');
        Route::put('/settings/general', [GeneralSettingsController::class, 'update'])->name('settings.general.update');
        Route::get('/settings/theme', [ThemeSettingsController::class, 'edit'])->name('settings.theme.edit');
        Route::put('/settings/theme', [ThemeSettingsController::class, 'update'])->name('settings.theme.update');
        Route::resource('social-links', SocialLinkController::class)->parameters(['social-links' => 'socialLink'])->except(['show']);
        Route::resource('menu-items', MenuItemController::class)->parameters(['menu-items' => 'menuItem'])->except(['show']);
        Route::resource('footer-links', FooterLinkController::class)->parameters(['footer-links' => 'footerLink'])->except(['show']);
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
