<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        $section = HomepageSection::query()->active()->where('section_key', 'services')->first();
        $services = Service::query()->active()->ordered()->paginate(12);

        return view('frontend.pages.services.index', compact('section', 'services'));
    }

    public function show(Service $service): View
    {
        abort_unless($service->status, 404);

        $relatedServices = Service::query()
            ->active()
            ->whereKeyNot($service->getKey())
            ->ordered()
            ->limit(4)
            ->get();

        return view('frontend.pages.services.show', compact('service', 'relatedServices'));
    }
}
