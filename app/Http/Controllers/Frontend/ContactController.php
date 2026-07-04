<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

        ContactMessage::query()->create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'is_read' => false,
        ]);

        Cache::forget('contact_messages_unread_count');

        return back()->with('success', 'Thank you. Your message has been sent successfully.');
    }
}
