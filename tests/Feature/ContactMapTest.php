<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ContactMapTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_admin_can_save_iframe_as_only_a_trusted_embed_url(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password', 'is_admin' => true]);

        $response = $this->actingAs($admin)->put(route('admin.settings.general.update'), [
            'company_name' => 'Example Construction',
            'show_contact_map' => '1',
            'google_map_embed_url' => '<iframe src="https://www.google.com/maps/embed?pb=test&amp;zoom=15" onload="alert(1)"></iframe><script>alert(2)</script>',
            'map_location_name' => 'Main Office',
            'map_address' => 'Dhaka, Bangladesh',
            'map_latitude' => '23.8103',
            'map_longitude' => '90.4125',
            'map_zoom' => '15',
        ]);

        $response->assertSessionHasNoErrors();
        $setting = SiteSetting::firstOrFail();
        $this->assertSame('https://www.google.com/maps/embed?pb=test&zoom=15', $setting->google_map_embed_url);
        $this->assertStringNotContainsString('<iframe', $setting->google_map_embed_url);
        $this->assertTrue($setting->show_contact_map);
    }

    public function test_admin_rejects_non_google_and_insecure_map_urls(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'password', 'is_admin' => true]);

        foreach (['https://evil.example/maps/embed', 'javascript:alert(1)', 'http://www.google.com/maps/embed?pb=test'] as $url) {
            $this->actingAs($admin)->put(route('admin.settings.general.update'), [
                'show_contact_map' => '1',
                'google_map_embed_url' => $url,
                'map_zoom' => 15,
            ])->assertSessionHasErrors('google_map_embed_url');
        }

        $this->assertDatabaseCount('site_settings', 0);
    }

    public function test_contact_page_renders_map_directions_and_existing_company_content(): void
    {
        SiteSetting::create([
            'company_name' => 'Example Construction HQ',
            'address' => 'Company address',
            'phone' => '+880123456',
            'email' => 'hello@example.com',
            'show_contact_map' => true,
            'google_map_embed_url' => 'https://www.google.com/maps/embed?pb=safe',
            'map_location_name' => 'Head Office',
            'map_latitude' => '23.8103',
            'map_longitude' => '90.4125',
        ]);
        SocialLink::create(['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/company/example', 'status' => true]);

        $this->get(route('contact.index'))
            ->assertOk()
            ->assertSee('src="https://www.google.com/maps/embed?pb=safe"', false)
            ->assertSee('title="Head Office"', false)
            ->assertSee('loading="lazy"', false)
            ->assertSee('Get Directions')
            ->assertSee(rawurlencode('23.8103000,90.4125000'), false)
            ->assertSee('Company address')
            ->assertSee('+880123456')
            ->assertSee('hello@example.com')
            ->assertSee('LinkedIn')
            ->assertSee('Send a Message')
            ->assertDontSee('Map or embedded location can be connected');
    }

    public function test_disabled_empty_or_tampered_map_settings_never_render_an_iframe(): void
    {
        $setting = SiteSetting::create([
            'company_name' => '<script>alert(1)</script>',
            'address' => 'Fallback address',
            'show_contact_map' => false,
            'google_map_embed_url' => 'https://www.google.com/maps/embed?pb=safe',
        ]);

        $this->get(route('contact.index'))->assertOk()->assertDontSee('<iframe', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertSee('Fallback address');

        $setting->update(['show_contact_map' => true, 'google_map_embed_url' => 'https://evil.example/embed']);
        Cache::forget('site_settings');
        $this->get(route('contact.index'))->assertOk()->assertDontSee('<iframe', false);

        $setting->update(['google_map_embed_url' => null, 'map_address' => null, 'address' => null]);
        Cache::forget('site_settings');
        $this->get(route('contact.index'))->assertOk()->assertDontSee('<iframe', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_existing_contact_form_still_submits(): void
    {
        $this->post(route('contact.store'), [
            'full_name' => 'Map Visitor',
            'email' => 'visitor@example.com',
            'phone' => '123',
            'subject' => 'Directions',
            'message' => 'Please contact me.',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'full_name' => 'Map Visitor',
            'email' => 'visitor@example.com',
        ]);
        $this->assertSame(1, ContactMessage::count());
    }
}
