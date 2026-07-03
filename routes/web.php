<?php

use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services.index');
Route::get('/projects', [PageController::class, 'projects'])->name('projects.index');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery.index');
Route::get('/contact', [PageController::class, 'contact'])->name('contact.index');
