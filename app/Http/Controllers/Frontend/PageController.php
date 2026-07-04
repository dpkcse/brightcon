<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FeatureItem;
use App\Models\HomepageSection;
use App\Models\Project;
use App\Models\Service;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $sliders = Cache::rememberForever('sliders_active_ordered', fn () => Slider::query()->active()->ordered()->get());
        $featureItems = Cache::rememberForever('feature_items_active_ordered', fn () => FeatureItem::query()->active()->ordered()->get());
        $aboutSection = $this->homepageSection('about', 'homepage_section_about');
        $projectHighlightsSection = $this->homepageSection('project_highlights', 'homepage_section_project_highlights');
        $galleryCtaSection = $this->homepageSection('gallery_cta', 'homepage_section_gallery_cta');
        $servicesSection = $this->homepageSection('services', 'homepage_section_services');
        $featuredProjects = Cache::rememberForever('homepage_projects_featured', fn () => Project::query()
            ->with('category')
            ->active()
            ->featured()
            ->ordered()
            ->get());
        $services = Cache::rememberForever('homepage_services_active', fn () => Service::query()->active()->ordered()->get());

        return view('frontend.pages.home', compact(
            'sliders',
            'featureItems',
            'aboutSection',
            'projectHighlightsSection',
            'galleryCtaSection',
            'servicesSection',
            'featuredProjects',
            'services'
        ));
    }

    public function about(): View { return view('frontend.pages.about'); }
    public function services(): View { return view('frontend.pages.services'); }
    public function projects(): View { return view('frontend.pages.projects'); }
    public function gallery(): View { return view('frontend.pages.gallery'); }
    public function contact(): View { return view('frontend.pages.contact'); }
    public function competency(): View { return view('frontend.pages.competency'); }
    public function equipment(): View { return view('frontend.pages.equipment-list'); }

    private function homepageSection(string $sectionKey, string $cacheKey): ?HomepageSection
    {
        return Cache::rememberForever($cacheKey, fn () => HomepageSection::query()
            ->active()
            ->where('section_key', $sectionKey)
            ->first());
    }
}
