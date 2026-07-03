<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSocialLinkRequest;
use App\Http\Requests\Admin\UpdateSocialLinkRequest;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SocialLinkController extends Controller
{
    public function index(): View
    {
        $items = SocialLink::query()->ordered()->paginate(15);
        return view('admin.pages.social-links.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.pages.social-links.form', ['item' => new SocialLink()]);
    }

    public function store(StoreSocialLinkRequest $request): RedirectResponse
    {
        SocialLink::create($this->payload($request->validated()));
        Cache::forget('social_links_active_ordered');
        return redirect()->route('admin.social-links.index')->with('success', 'Record created successfully.');
    }

    public function edit(SocialLink $socialLink): View
    {
        return view('admin.pages.social-links.form', ['item' => $socialLink]);
    }

    public function update(UpdateSocialLinkRequest $request, SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update($this->payload($request->validated()));
        Cache::forget('social_links_active_ordered');
        return redirect()->route('admin.social-links.index')->with('success', 'Record updated successfully.');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();
        Cache::forget('social_links_active_ordered');
        return back()->with('success', 'Record deleted successfully.');
    }

    private function payload(array $data): array
    {
        $data['status'] = (bool) ($data['status'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        return $data;
    }
}
