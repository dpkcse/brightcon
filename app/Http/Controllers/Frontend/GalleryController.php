<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = GalleryImage::query()->active()->whereNotNull('category')->select('category')->distinct()->orderBy('category')->pluck('category');
        $activeCategory = $categories->first(fn ($category) => $category === $request->query('category'));
        $images = GalleryImage::query()
            ->active()
            ->when($activeCategory, fn ($query) => $query->where('category', $activeCategory))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('frontend.pages.gallery.index', compact('categories', 'activeCategory', 'images'));
    }
}
