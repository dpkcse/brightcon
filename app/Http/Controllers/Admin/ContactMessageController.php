<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ContactReply;
use App\Models\ContactMessage;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        $messages = ContactMessage::query()->when($request->filled('search'), function ($q) use ($request) {
            $s = '%'.$request->string('search')->toString().'%';
            $q->where(function ($q) use ($s) {
                $q->where('full_name', 'like', $s)->orWhere('email', 'like', $s)->orWhere('phone', 'like', $s)->orWhere('subject', 'like', $s)->orWhere('message', 'like', $s);
            });
        })->when($request->filled('status'), fn ($q) => $q->where('workflow_status', $request->status))->latest()->paginate(15)->withQueryString();

        return view('admin.pages.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $contactMessage): View
    {
        if ($contactMessage->workflow_status === 'unread') {
            $contactMessage->update(['workflow_status' => 'read', 'is_read' => true]);
        }

        return view('admin.pages.contact-messages.show', ['message' => $contactMessage]);
    }

    public function markRead(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['is_read' => true, 'workflow_status' => 'read']);
        Cache::forget('contact_messages_unread_count');

        return back()->with('success', 'Message marked as read.');
    }

    public function markUnread(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->update(['is_read' => false, 'workflow_status' => 'unread']);
        Cache::forget('contact_messages_unread_count');

        return back()->with('success', 'Message marked as unread.');
    }

    public function status(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $d = $request->validate(['workflow_status' => 'required|in:unread,read,replied,archived', 'internal_note' => 'nullable|string|max:5000']);
        $d['is_read'] = $d['workflow_status'] !== 'unread';
        $d['archived_at'] = $d['workflow_status'] === 'archived' ? now() : null;
        $contactMessage->update($d);
        Cache::forget('contact_messages_unread_count');

        return back()->with('success', 'Workflow updated.');
    }

    public function reply(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $d = $request->validate(['reply_body' => 'required|string|max:10000']);
        if (RuntimeDemoMode::enabled()) {
            return back()->withErrors(['reply_body' => 'External replies are suppressed in runtime demo mode.']);
        }

        try {
            Mail::to($contactMessage->email)->send(new ContactReply($contactMessage, $d['reply_body']));
            $contactMessage->update(['workflow_status' => 'replied', 'is_read' => true, 'replied_at' => now(), 'replied_by' => $request->user()->id]);

            return back()->with('success', 'Reply sent.');
        } catch (Throwable) {
            return back()->withErrors(['reply_body' => 'The reply could not be delivered. No sensitive transport details were stored.']);
        }
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        $contactMessage->update(['workflow_status' => 'archived', 'archived_at' => now(), 'is_read' => true]);
        Cache::forget('contact_messages_unread_count');

        return redirect()->route('admin.contact-messages.index')->with('success', 'Message archived.');
    }
}
