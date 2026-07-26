<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\View\View;

class CustomPageController extends Controller
{
    public function show(string $page): View
    {
        $item = Page::query()->published()->where('slug', $page)->firstOrFail();

        return view('frontend.pages.custom-page', ['page' => $item]);
    }

    public function preview(Page $page): View
    {
        return view('frontend.pages.custom-page', compact('page'));
    }
}
