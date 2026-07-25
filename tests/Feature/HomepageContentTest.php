<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\PartnerMessage;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HomepageContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_core_public_pages_and_existing_detail_routes_still_load(): void
    {
        $service = Service::create(['title' => 'Civil Construction', 'slug' => 'civil-construction', 'status' => true, 'is_featured' => true]);
        $project = Project::create(['title' => 'Airport Works', 'slug' => 'airport-works', 'status' => true, 'is_featured' => true]);

        foreach (['/', '/services', route('services.show', $service), '/projects', route('projects.show', $project), '/contact'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_homepage_uses_active_featured_services_in_display_order_and_limits_results(): void
    {
        Service::create(['title' => 'Hidden inactive', 'slug' => 'hidden-inactive', 'status' => false, 'is_featured' => true, 'sort_order' => 0]);
        Service::create(['title' => 'Not featured', 'slug' => 'not-featured', 'status' => true, 'is_featured' => false, 'sort_order' => 0]);
        foreach ([4, 2, 5, 1, 3] as $order) {
            Service::create(['title' => "Service $order", 'slug' => "service-$order", 'status' => true, 'is_featured' => true, 'sort_order' => $order]);
        }

        $this->get('/')->assertOk()->assertSeeInOrder(['Service 1', 'Service 2', 'Service 3', 'Service 4'])->assertDontSee('Service 5')->assertDontSee('Hidden inactive')->assertDontSee('Not featured')->assertSee(route('services.show', 'service-1'), false);
        $this->get('/services')->assertOk()->assertSee('Not featured')->assertDontSee('Hidden inactive');
    }

    public function test_homepage_uses_only_active_featured_projects_in_order_and_keeps_factual_heading(): void
    {
        $category = ProjectCategory::create(['name' => 'Civil', 'slug' => 'civil', 'status' => true]);
        Project::create(['project_category_id' => $category->id, 'title' => 'Inactive project', 'slug' => 'inactive-project', 'status' => false, 'is_featured' => true]);
        Project::create(['title' => 'Unfeatured project', 'slug' => 'unfeatured-project', 'status' => true, 'is_featured' => false]);
        foreach ([2, 1] as $order) {
            Project::create(['project_category_id' => $category->id, 'title' => "Project $order", 'slug' => "project-$order", 'status' => true, 'is_featured' => true, 'sort_order' => $order]);
        }

        $this->get('/')->assertOk()->assertSee('Project Highlights')->assertSeeInOrder(['Project 1', 'Project 2'])->assertDontSee('Inactive project')->assertDontSee('Unfeatured project')->assertSee(route('projects.show', 'project-1'), false);
        $this->get('/projects')->assertOk()->assertSee('Unfeatured project')->assertDontSee('Inactive project');
    }

    public function test_organization_grid_hides_inactive_or_unfeatured_items_orders_records_and_falls_back_to_name(): void
    {
        Organization::create(['name' => 'Second Organization', 'is_active' => true, 'is_featured' => true, 'display_order' => 2]);
        Organization::create(['name' => 'First Organization', 'is_active' => true, 'is_featured' => true, 'display_order' => 1]);
        Organization::create(['name' => 'Inactive Organization', 'is_active' => false, 'is_featured' => true]);
        Organization::create(['name' => 'Unfeatured Organization', 'is_active' => true, 'is_featured' => false]);

        $this->get('/')->assertOk()->assertSee('Selected Project Experience')->assertSeeInOrder(['First Organization', 'Second Organization'])->assertDontSee('Inactive Organization')->assertDontSee('Unfeatured Organization')->assertSee('<span>First Organization</span>', false);
    }

    public function test_routes_are_unique_and_partner_messages_remain_available(): void
    {
        $names = collect(Route::getRoutes()->getRoutes())->pluck('action.as')->filter();
        $this->assertSame($names->count(), $names->unique()->count());
        PartnerMessage::create(['name' => 'Existing Partner', 'designation' => 'Director', 'full_message' => 'Existing partner content', 'is_active' => true]);
        $this->get('/about')->assertOk()->assertSee('Existing Partner')->assertSee('Company Profile');
    }
}
