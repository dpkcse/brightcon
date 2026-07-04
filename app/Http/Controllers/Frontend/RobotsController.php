<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url') ?: request()->root(), '/');

        return response("User-agent: *\nDisallow: /admin\nAllow: /\nSitemap: {$baseUrl}/sitemap.xml\n", 200)
            ->header('Content-Type', 'text/plain');
    }
}
