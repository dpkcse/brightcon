<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\GalleryImage;
use App\Models\Project;
use App\Models\Service;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.dashboard', [
            'totalProjects' => Project::query()->count(),
            'totalServices' => Service::query()->count(),
            'totalGalleryImages' => GalleryImage::query()->count(),
            'totalContactMessages' => ContactMessage::query()->count(),
            'unreadContactMessages' => ContactMessage::query()->unread()->count(),
            'recentContactMessages' => ContactMessage::query()->latest()->take(5)->get(),
        ]);
    }
}
