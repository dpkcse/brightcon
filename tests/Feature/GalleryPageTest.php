<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class GalleryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_only_shows_active_images_and_exposes_safe_lightbox_data(): void
    {
        GalleryImage::create(['title' => 'Airport terminal works', 'image' => 'uploads/gallery/airport.jpg', 'category' => 'Projects', 'status' => true]);
        GalleryImage::create(['title' => 'Private inactive image', 'image' => 'uploads/gallery/private.jpg', 'category' => 'Projects', 'status' => false]);

        $response = $this->get(route('gallery.index'));

        $response->assertOk()
            ->assertSee('Project Gallery')
            ->assertSee('Airport terminal works')
            ->assertDontSee('Private inactive image')
            ->assertSee('data-gallery-item', false)
            ->assertSee('data-full-image="/storage/uploads/gallery/airport.jpg"', false)
            ->assertSee('data-gallery-lightbox', false)
            ->assertSee('aria-label="Close image viewer"', false);
    }

    public function test_category_filtering_and_pagination_preserve_the_selected_category(): void
    {
        foreach (range(1, 13) as $index) {
            GalleryImage::create([
                'title' => "Steel work $index",
                'image' => "uploads/gallery/steel-$index.jpg",
                'category' => 'Metal & Steel Works',
                'sort_order' => $index,
                'status' => true,
            ]);
        }
        GalleryImage::create(['title' => 'PVC door', 'image' => 'uploads/gallery/pvc.jpg', 'category' => 'PVC Doors', 'status' => true]);

        $response = $this->get(route('gallery.index', ['category' => 'Metal & Steel Works']));

        $response->assertOk()
            ->assertSee('Steel work 1')
            ->assertDontSee('PVC door')
            ->assertSee('category=Metal%20%26%20Steel%20Works', false)
            ->assertSee('data-gallery-pagination', false)
            ->assertDontSee('<svg', false);

        $this->assertSame(1, substr_count($response->getContent(), 'data-gallery-pagination'));
        $this->assertSame(12, substr_count($response->getContent(), 'data-gallery-item'));
    }

    public function test_missing_gallery_image_uses_the_non_interactive_fallback(): void
    {
        GalleryImage::create(['title' => 'Pending photograph', 'image' => '', 'category' => 'Projects', 'status' => true]);

        $this->get(route('gallery.index'))
            ->assertOk()
            ->assertSee('Pending photograph')
            ->assertSee('Image unavailable')
            ->assertDontSee('data-gallery-item', false);
    }

    public function test_gallery_routes_remain_unique_and_admin_crud_remains_protected(): void
    {
        $galleryRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => $route->getName() === 'gallery.index');

        $this->assertCount(1, $galleryRoutes);
        $this->get(route('admin.gallery-images.index'))->assertRedirect(route('admin.login'));
        $this->get(route('gallery.index'))->assertSee('Gallery')->assertSee('Contact');
    }
}
