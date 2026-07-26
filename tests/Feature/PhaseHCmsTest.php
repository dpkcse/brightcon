<?php

namespace Tests\Feature;

use App\Mail\ContactNotification;
use App\Models\Competency;
use App\Models\ContactMessage;
use App\Models\Equipment;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Settings\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PhaseHCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_page_visibility_sanitization_seo_and_sitemap(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('password'), 'is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.pages.store'), ['title' => 'Safety Policy', 'slug' => 'safety-policy', 'content' => '<h2 onclick="bad()">Safe</h2><script>alert(1)</script><a href="javascript:alert(2)">bad</a><a href="https://example.com">external</a>', 'status' => 'published', 'published_at' => now()->subMinute()])->assertRedirect(route('admin.pages.index'));
        $page = Page::firstOrFail();
        $this->assertStringNotContainsString('onclick', $page->content);
        $this->assertStringNotContainsString('<script', $page->content);
        $this->assertStringNotContainsString('javascript:', $page->content);
        $this->assertStringContainsString('noopener noreferrer', $page->content);
        $this->get(route('pages.show', $page))->assertOk()->assertSee('Safety Policy')->assertDontSee('alert(1)');
        $this->get('/sitemap.xml')->assertOk()->assertSee('/pages/safety-policy');
        $page->update(['status' => 'archived']);
        $this->get('/pages/safety-policy')->assertNotFound();
    }

    public function test_reserved_slug_and_admin_authorization(): void
    {
        $this->get('/admin/pages')->assertRedirect('/admin/login');
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('password'), 'is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.pages.store'), ['title' => 'Admin', 'slug' => 'admin', 'status' => 'draft'])->assertSessionHasErrors('slug');
    }

    public function test_hierarchical_typed_menu_and_dependency_safety(): void
    {
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('password'), 'is_admin' => true]);
        $page = Page::create(['title' => 'Policy', 'slug' => 'policy', 'status' => 'published']);
        $parent = MenuItem::create(['label' => 'Company', 'url' => '/about', 'menu_location' => 'header', 'link_type' => 'legacy', 'target' => '_self', 'status' => true]);
        $this->actingAs($admin)->post(route('admin.menu-items.store'), ['label' => 'Policy', 'menu_location' => 'header', 'parent_id' => $parent->id, 'link_type' => 'page', 'page_id' => $page->id, 'target' => '_self', 'status' => 1])->assertRedirect();
        $this->get('/')->assertOk()->assertSee('dropdown-menu', false)->assertSee('/pages/policy', false);
        $this->actingAs($admin)->delete(route('admin.pages.destroy', $page))->assertSessionHasErrors('page');
        $this->actingAs($admin)->put(route('admin.menu-items.update', $parent), ['label' => 'Company', 'menu_location' => 'header', 'parent_id' => $parent->id, 'link_type' => 'legacy', 'url' => '/about', 'target' => '_self', 'status' => 1])->assertSessionHasErrors('parent_id');
    }

    public function test_managed_equipment_and_competencies_filter_publication_without_changing_routes(): void
    {
        Equipment::create(['name' => 'Published Crane', 'slug' => 'published-crane', 'status' => 'published', 'display_order' => 2]);
        Equipment::create(['name' => 'Draft Crane', 'slug' => 'draft-crane', 'status' => 'draft']);
        Competency::create(['title' => 'Published Planning', 'slug' => 'published-planning', 'status' => 'published']);
        Competency::create(['title' => 'Archived Planning', 'slug' => 'archived-planning', 'status' => 'archived']);
        $this->get('/equipment-list')->assertOk()->assertSee('Published Crane')->assertDontSee('Draft Crane');
        $this->get('/competency')->assertOk()->assertSee('Published Planning')->assertDontSee('Archived Planning');
    }

    public function test_contact_is_stored_and_notification_uses_reply_to(): void
    {
        SiteSetting::create(['company_name' => 'Example', 'email' => 'sender@example.test', 'contact_recipient_email' => 'owner@example.test', 'contact_form_enabled' => true]);
        app(SettingsService::class)->refresh();
        Mail::fake();
        $this->post('/contact', ['full_name' => 'Visitor', 'email' => 'visitor@example.test', 'subject' => 'Hello', 'message' => 'A valid message', 'website' => null])->assertRedirect();
        $message = ContactMessage::firstOrFail();
        $this->assertSame('delivered', $message->delivery_status);
        Mail::assertSent(ContactNotification::class, fn ($mail) => $mail->hasTo('owner@example.test') && $mail->hasReplyTo('visitor@example.test'));
    }

    public function test_runtime_demo_suppresses_delivery_but_keeps_submission(): void
    {
        config(['cms.runtime_demo_mode' => true]);
        SiteSetting::create(['company_name' => 'Example', 'email' => 'sender@example.test', 'contact_recipient_email' => 'owner@example.test', 'contact_form_enabled' => true]);
        app(SettingsService::class)->refresh();
        Mail::fake();
        $this->post('/contact', ['full_name' => 'Visitor', 'email' => 'visitor@example.test', 'message' => 'Stored in demo', 'website' => null])->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['email' => 'visitor@example.test', 'delivery_status' => 'suppressed', 'delivery_failure_code' => 'demo_mode']);
        Mail::assertNothingSent();
    }

    public function test_reply_workflow_is_admin_only_and_records_safe_reply(): void
    {
        Mail::fake();
        $message = ContactMessage::create(['full_name' => 'Visitor', 'email' => 'visitor@example.test', 'message' => 'Question', 'is_read' => false]);
        $this->post(route('admin.contact-messages.reply', $message), ['reply_body' => 'Answer'])->assertRedirect('/admin/login');
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@example.test', 'password' => Hash::make('password'), 'is_admin' => true]);
        $this->actingAs($admin)->post(route('admin.contact-messages.reply', $message), ['reply_body' => 'Safe answer'])->assertSessionHas('success');
        $message->refresh();
        $this->assertSame('replied', $message->workflow_status);
        $this->assertNotNull($message->replied_at);
        $this->assertSame($admin->id, $message->replied_by);
    }
}
