<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ProjectCategory::query()->active()->ordered()->get();
        $activeCategory = $categories->firstWhere('slug', $request->query('category'));

        $projects = Project::query()
            ->with('category')
            ->active()
            ->when($activeCategory, fn ($query) => $query->where('project_category_id', $activeCategory->id))
            ->ordered()
            ->paginate(9)
            ->withQueryString();

        return view('frontend.pages.projects.index', compact('categories', 'activeCategory', 'projects'));
    }

    public function show(Project $project): View
    {
        abort_unless($project->status, 404);

        $project->load('category');

        $relatedProjects = Project::query()
            ->with('category')
            ->active()
            ->whereKeyNot($project->getKey())
            ->when($project->project_category_id, fn ($query) => $query->where('project_category_id', $project->project_category_id))
            ->ordered()
            ->limit(3)
            ->get();

        if ($relatedProjects->count() < 3) {
            $fallbackProjects = Project::query()
                ->with('category')
                ->active()
                ->whereKeyNot($project->getKey())
                ->whereNotIn('id', $relatedProjects->pluck('id'))
                ->ordered()
                ->limit(3 - $relatedProjects->count())
                ->get();
            $relatedProjects = $relatedProjects->concat($fallbackProjects);
        }

        return view('frontend.pages.projects.show', compact('project', 'relatedProjects'));
    }
}
