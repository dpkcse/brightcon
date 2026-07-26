<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return back()->with('success', 'Thank you. Your message has been sent successfully.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
            'website' => ['nullable', 'prohibited'],
        ]);

        unset($validated['website']);

        $settings = app(SettingsRepositoryInterface::class);
        if (! $settings->bool('contact_form_enabled', true)) {
            abort(404);
        }
        $contactMessage = ContactMessage::query()->create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_read' => false,
            'workflow_status' => 'unread', 'delivery_status' => 'pending',
        ]);

        $recipient = $settings->string('contact_recipient_email') ?: $settings->string('email');
        if (RuntimeDemoMode::enabled()) {
            $contactMessage->update(['delivery_status' => 'suppressed', 'delivery_failure_code' => 'demo_mode']);
        } elseif ($recipient && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($recipient)->send(new ContactNotification($contactMessage, $settings->string('contact_email_subject_prefix') ?: 'Website enquiry'));
                $contactMessage->update(['delivery_status' => 'delivered', 'delivered_at' => now(), 'delivery_failure_code' => null]);
            } catch (Throwable) {
                $contactMessage->update(['delivery_status' => 'failed', 'delivery_failure_code' => 'transport_failure']);
            }
        } else {
            $contactMessage->update(['delivery_status' => 'not_configured', 'delivery_failure_code' => 'recipient_missing']);
        }

        Cache::forget('contact_messages_unread_count');

        return back()->with('success', $settings->string('contact_success_message') ?: 'Thank you. Your message has been sent successfully.');
    }
}
