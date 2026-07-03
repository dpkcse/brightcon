<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\FeatureItemController;
use App\Http\Controllers\Admin\FooterLinkController;
use App\Http\Controllers\Admin\HomepageSectionController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\Settings\GeneralSettingsController;
use App\Http\Controllers\Admin\Settings\ThemeSettingsController;
use App\Http\Controllers\Admin\SliderController;
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
        Route::resource('sliders', SliderController::class)->except(['show']);
        Route::resource('features', FeatureItemController::class)->parameters(['features' => 'feature'])->except(['show']);
        Route::get('/homepage-sections', [HomepageSectionController::class, 'index'])->name('homepage-sections.index');
        Route::get('/homepage-sections/{homepageSection}/edit', [HomepageSectionController::class, 'edit'])->name('homepage-sections.edit');
        Route::put('/homepage-sections/{homepageSection}', [HomepageSectionController::class, 'update'])->name('homepage-sections.update');
        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::patch('/contact-messages/{contactMessage}/mark-read', [ContactMessageController::class, 'markRead'])->name('contact-messages.mark-read');
        Route::patch('/contact-messages/{contactMessage}/mark-unread', [ContactMessageController::class, 'markUnread'])->name('contact-messages.mark-unread');
        Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});
