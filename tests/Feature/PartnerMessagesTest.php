<?php

namespace Tests\Feature;

use App\Models\PartnerMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PartnerMessagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('partner_messages_public');
    }

    public function test_about_only_renders_active_published_messages_in_order(): void
    {
        PartnerMessage::create(['name' => 'Second Partner', 'designation' => 'Director', 'full_message' => 'Second public message', 'display_order' => 2, 'is_active' => true, 'published_at' => now()->subDay()]);
        PartnerMessage::create(['name' => 'First Partner', 'designation' => 'Director', 'full_message' => 'First public message', 'display_order' => 1, 'is_active' => true, 'published_at' => now()->subDay()]);
        PartnerMessage::create(['name' => 'Hidden Partner', 'designation' => 'Director', 'full_message' => 'Hidden message', 'is_active' => false, 'published_at' => now()->subDay()]);
        PartnerMessage::create(['name' => 'Draft Partner', 'designation' => 'Director', 'full_message' => 'Draft message', 'is_active' => true]);

        $response = $this->get('/about');

        $response->assertOk()->assertSee('Messages from Our Partners')->assertSeeInOrder(['First Partner', 'Second Partner'])->assertDontSee('Hidden Partner')->assertDontSee('Draft Partner')->assertSee('data-partner-prev', false);
    }

    public function test_a_single_partner_omits_slider_controls(): void
    {
        PartnerMessage::create(['name' => 'Only Partner', 'designation' => 'Director', 'full_message' => 'One message', 'is_active' => true, 'published_at' => now()->subDay()]);
        $this->get('/about')->assertOk()->assertDontSee('data-partner-prev', false);
    }

    public function test_guests_cannot_manage_partner_messages_and_admins_can_create_them(): void
    {
        $this->get('/admin/partner-messages')->assertRedirect('/admin/login');
        $admin = User::query()->create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('password')]);
        $this->actingAs($admin)->post('/admin/partner-messages', ['name' => 'Partner', 'designation' => 'Chair', 'full_message' => 'A safe message', 'linkedin_url' => 'not-a-url'])->assertSessionHasErrors('linkedin_url');
        $this->actingAs($admin)->post('/admin/partner-messages', ['name' => 'Partner', 'designation' => 'Chair', 'full_message' => 'A safe message', 'is_active' => 1, 'published_at' => now()->format('Y-m-d H:i:s')])->assertRedirect(route('admin.partner-messages.index'));
        $this->assertDatabaseHas('partner_messages', ['name' => 'Partner', 'is_active' => 1]);
    }
}
