<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View { return view('frontend.pages.home'); }
    public function about(): View { return view('frontend.pages.about'); }
    public function services(): View { return view('frontend.pages.services'); }
    public function projects(): View { return view('frontend.pages.projects'); }
    public function gallery(): View { return view('frontend.pages.gallery'); }
    public function contact(): View { return view('frontend.pages.contact'); }
}
