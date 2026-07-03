<?php

namespace Database\Seeders;

use App\Models\FeatureItem;
use App\Models\FooterLink;
use App\Models\GalleryImage;
use App\Models\HomepageSection;
use App\Models\MenuItem;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Slider;
use App\Models\SocialLink;
use App\Models\ThemeSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Local Admin',
                'password' => Hash::make('password'),
            ]
        );

        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'company_name' => 'Premium Engineering & Construction Ltd.',
            'tagline' => 'Extraordinary Reach, Extraordinary Results',
            'email' => 'info@example.com',
            'phone' => '+880 1700 000000',
            'address' => 'Dhaka, Bangladesh',
            'default_language' => 'en',
            'timezone' => 'Asia/Dhaka',
            'copyright_text' => 'All Rights Reserved.',
            'developer_name' => 'Your Company',
            'developer_link' => '#',
        ]);

        ThemeSetting::query()->updateOrCreate(['id' => 1], [
            'primary_color' => '#d80d4c',
            'secondary_color' => '#ffffff',
            'footer_background_color' => '#111827',
            'body_text_color' => '#555555',
            'heading_text_color' => '#222222',
            'body_font_family' => 'Inter, sans-serif',
            'heading_font_family' => 'Poppins, sans-serif',
            'base_font_size' => '16px',
            'h1_font_size' => '48px',
            'h2_font_size' => '36px',
            'h3_font_size' => '24px',
            'button_radius' => '30px',
            'section_spacing' => '80px',
        ]);

        foreach ([
            ['platform' => 'Facebook', 'icon_class' => 'fab fa-facebook-f', 'url' => 'https://facebook.com/example', 'sort_order' => 1],
            ['platform' => 'Twitter / X', 'icon_class' => 'fab fa-x-twitter', 'url' => 'https://x.com/example', 'sort_order' => 2],
            ['platform' => 'LinkedIn', 'icon_class' => 'fab fa-linkedin-in', 'url' => 'https://linkedin.com/company/example', 'sort_order' => 3],
        ] as $link) {
            SocialLink::query()->updateOrCreate(['platform' => $link['platform']], $link);
        }

        foreach ([
            ['label' => 'Home', 'url' => '/', 'sort_order' => 1],
            ['label' => 'About', 'url' => '/about', 'sort_order' => 2],
            ['label' => 'Services', 'url' => '/services', 'sort_order' => 3],
            ['label' => 'Projects', 'url' => '/projects', 'sort_order' => 4],
            ['label' => 'Competency', 'url' => '/competency', 'sort_order' => 5],
            ['label' => 'Equipment List', 'url' => '/equipment-list', 'sort_order' => 6],
            ['label' => 'Gallery', 'url' => '/gallery', 'sort_order' => 7],
            ['label' => 'Contact', 'url' => '/contact', 'sort_order' => 8],
        ] as $item) {
            MenuItem::query()->updateOrCreate(['label' => $item['label']], $item);
        }

        foreach ([
            ['heading' => 'Building Tomorrow\'s Infrastructure', 'sub_heading' => 'Engineering Excellence', 'description' => 'Delivering safe, durable, and scalable construction solutions across Bangladesh.', 'button_text' => 'Explore Projects', 'button_link' => '/projects', 'sort_order' => 1],
            ['heading' => 'Powering Growth Through Construction', 'sub_heading' => 'Trusted Delivery Partner', 'description' => 'From power distribution to landmark structures, we manage complex projects with precision.', 'button_text' => 'Our Services', 'button_link' => '/services', 'sort_order' => 2],
        ] as $slide) {
            Slider::query()->updateOrCreate(['heading' => $slide['heading']], $slide);
        }

        foreach ([
            ['title' => 'Punctual Delivery Time', 'short_text' => 'Disciplined planning and field execution keep critical construction milestones on track.', 'icon_class' => 'fa-solid fa-clock', 'sort_order' => 1],
            ['title' => 'High Technology Factory', 'short_text' => 'Modern engineering methods and equipment support accurate, efficient project delivery.', 'icon_class' => 'fa-solid fa-industry', 'sort_order' => 2],
            ['title' => 'High Standard Labors', 'short_text' => 'Experienced teams follow safety-first practices and high workmanship standards.', 'icon_class' => 'fa-solid fa-helmet-safety', 'sort_order' => 3],
        ] as $feature) {
            FeatureItem::query()->updateOrCreate(['title' => $feature['title']], $feature);
        }

        foreach ([
            ['section_key' => 'about', 'title' => 'About Our Company', 'subtitle' => 'Reliable engineering and construction services', 'content' => 'We plan, build, and deliver infrastructure, building, land development, and power-sector projects for public and private clients.', 'button_text' => 'Learn More', 'button_link' => '/about', 'sort_order' => 1],
            ['section_key' => 'project_highlights', 'title' => 'Project Highlights', 'subtitle' => 'Selected work across core sectors', 'content' => 'Explore featured projects that demonstrate our technical capability, safety culture, and delivery performance.', 'button_text' => 'View Projects', 'button_link' => '/projects', 'sort_order' => 2],
            ['section_key' => 'gallery_cta', 'title' => 'Site Progress & Gallery', 'subtitle' => 'Snapshots from our active and completed works', 'content' => 'Browse construction progress, equipment, and completed project images from our portfolio.', 'button_text' => 'Open Gallery', 'button_link' => '/gallery', 'sort_order' => 3],
            ['section_key' => 'services', 'title' => 'Our Services', 'subtitle' => 'Integrated construction capabilities', 'content' => 'We provide civil, structural, road, bridge, power, and building construction services from planning through handover.', 'button_text' => 'All Services', 'button_link' => '/services', 'sort_order' => 4],
        ] as $section) {
            HomepageSection::query()->updateOrCreate(['section_key' => $section['section_key']], $section);
        }

        $categories = collect([
            ['name' => 'Power & Energy', 'description' => 'Electrical infrastructure and power distribution works.', 'sort_order' => 1],
            ['name' => 'Infrastructure', 'description' => 'Roads, bridges, rail, and public infrastructure projects.', 'sort_order' => 2],
            ['name' => 'Building Construction', 'description' => 'Commercial, industrial, and institutional building works.', 'sort_order' => 3],
            ['name' => 'Land Development', 'description' => 'Earthworks, grading, and site development services.', 'sort_order' => 4],
        ])->mapWithKeys(function (array $category): array {
            $category['slug'] = Str::slug($category['name']);

            return [$category['slug'] => ProjectCategory::query()->updateOrCreate(['slug' => $category['slug']], $category)];
        });

        foreach ([
            ['title' => 'Power Distribution', 'category' => 'power-energy', 'progress_percentage' => 85, 'is_featured' => true, 'sort_order' => 1],
            ['title' => 'Land Development', 'category' => 'land-development', 'progress_percentage' => 65, 'is_featured' => true, 'sort_order' => 2],
            ['title' => 'Building Construction', 'category' => 'building-construction', 'progress_percentage' => 50, 'is_featured' => false, 'sort_order' => 3],
            ['title' => 'Metro Rail Projects', 'category' => 'infrastructure', 'progress_percentage' => 35, 'is_featured' => true, 'sort_order' => 4],
        ] as $project) {
            $slug = Str::slug($project['title']);
            Project::query()->updateOrCreate(['slug' => $slug], [
                'project_category_id' => $categories[$project['category']]?->id,
                'title' => $project['title'],
                'progress_percentage' => $project['progress_percentage'],
                'short_description' => 'Demo project summary for '.$project['title'].'.',
                'full_description' => 'Detailed project information will be managed from the CMS in a later phase.',
                'status' => true,
                'is_featured' => $project['is_featured'],
                'sort_order' => $project['sort_order'],
                'seo_title' => $project['title'],
                'seo_description' => 'Learn more about '.$project['title'].' by Premium Engineering & Construction Ltd.',
            ]);
        }

        foreach ([
            ['title' => 'Roads & Highway', 'icon_class' => 'fa-solid fa-road', 'sort_order' => 1],
            ['title' => 'Flyover & Bridge', 'icon_class' => 'fa-solid fa-bridge', 'sort_order' => 2],
            ['title' => 'Power & Energy', 'icon_class' => 'fa-solid fa-bolt', 'sort_order' => 3],
            ['title' => 'Building & Structure', 'icon_class' => 'fa-solid fa-building', 'sort_order' => 4],
        ] as $service) {
            $slug = Str::slug($service['title']);
            Service::query()->updateOrCreate(['slug' => $slug], [
                'title' => $service['title'],
                'icon_class' => $service['icon_class'],
                'short_description' => 'Professional '.$service['title'].' construction and engineering services.',
                'full_description' => 'Complete service details will be maintained through CMS content management in a later phase.',
                'status' => true,
                'sort_order' => $service['sort_order'],
                'seo_title' => $service['title'],
                'seo_description' => 'Explore '.$service['title'].' services from Premium Engineering & Construction Ltd.',
            ]);
        }

        foreach ([
            ['title' => 'Construction Site Progress', 'image' => 'placeholders/gallery/construction-site.jpg', 'category' => 'Projects', 'sort_order' => 1],
            ['title' => 'Bridge Engineering Work', 'image' => 'placeholders/gallery/bridge-work.jpg', 'category' => 'Infrastructure', 'sort_order' => 2],
            ['title' => 'Power Distribution Setup', 'image' => 'placeholders/gallery/power-distribution.jpg', 'category' => 'Power & Energy', 'sort_order' => 3],
        ] as $image) {
            GalleryImage::query()->updateOrCreate(['image' => $image['image']], $image);
        }

        foreach ([
            ['label' => 'Company Information', 'url' => '/about', 'sort_order' => 1],
            ['label' => 'Overview', 'url' => '/about#overview', 'sort_order' => 2],
            ['label' => 'Mission & Vision', 'url' => '/about#mission-vision', 'sort_order' => 3],
            ['label' => 'Projects in Hand', 'url' => '/projects?status=in-hand', 'sort_order' => 4],
            ['label' => 'Completed Projects', 'url' => '/projects?status=completed', 'sort_order' => 5],
            ['label' => 'Competency', 'url' => '/competency', 'sort_order' => 6],
            ['label' => 'Gallery', 'url' => '/gallery', 'sort_order' => 7],
            ['label' => 'Contact', 'url' => '/contact', 'sort_order' => 8],
        ] as $link) {
            FooterLink::query()->updateOrCreate(['label' => $link['label']], $link);
        }
    }
}
