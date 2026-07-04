<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $baseUrl = rtrim(config('app.url') ?: request()->root(), '/');
        $staticUpdatedAt = Carbon::now()->toDateString();

        $services = Cache::remember('sitemap_services_active', now()->addHour(), fn () => Service::query()
            ->active()
            ->select(['slug', 'updated_at'])
            ->ordered()
            ->get());

        $projects = Cache::remember('sitemap_projects_active', now()->addHour(), fn () => Project::query()
            ->active()
            ->select(['slug', 'updated_at'])
            ->ordered()
            ->get());

        $urls = collect([
            ['loc' => route('home'), 'lastmod' => $staticUpdatedAt],
            ['loc' => route('about'), 'lastmod' => $staticUpdatedAt],
            ['loc' => route('services.index'), 'lastmod' => $staticUpdatedAt],
        ]);

        foreach ($services as $service) {
            $urls->push(['loc' => route('services.show', $service), 'lastmod' => optional($service->updated_at)->toDateString()]);
        }

        $urls = $urls->merge([
            ['loc' => route('projects.index'), 'lastmod' => $staticUpdatedAt],
        ]);

        foreach ($projects as $project) {
            $urls->push(['loc' => route('projects.show', $project), 'lastmod' => optional($project->updated_at)->toDateString()]);
        }

        $urls = $urls->merge([
            ['loc' => route('competency'), 'lastmod' => $staticUpdatedAt],
            ['loc' => route('equipment.index'), 'lastmod' => $staticUpdatedAt],
            ['loc' => route('gallery.index'), 'lastmod' => $staticUpdatedAt],
            ['loc' => route('contact.index'), 'lastmod' => $staticUpdatedAt],
        ])->map(fn (array $url) => [
            'loc' => str_starts_with($url['loc'], 'http') ? $url['loc'] : $baseUrl.$url['loc'],
            'lastmod' => $url['lastmod'] ?: $staticUpdatedAt,
        ]);

        return response()
            ->view('frontend.sitemap', ['urls' => $urls], 200)
            ->header('Content-Type', 'application/xml');
    }
}
