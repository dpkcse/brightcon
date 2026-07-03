<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFooterLinkRequest;
use App\Http\Requests\Admin\UpdateFooterLinkRequest;
use App\Models\FooterLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FooterLinkController extends Controller
{
    public function index(): View
    {
        $items = FooterLink::query()->ordered()->paginate(15);
        return view('admin.pages.footer-links.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.pages.footer-links.form', ['item' => new FooterLink()]);
    }

    public function store(StoreFooterLinkRequest $request): RedirectResponse
    {
        FooterLink::create($this->payload($request->validated()));
        Cache::forget('footer_links_active_ordered');
        return redirect()->route('admin.footer-links.index')->with('success', 'Record created successfully.');
    }

    public function edit(FooterLink $footerLink): View
    {
        return view('admin.pages.footer-links.form', ['item' => $footerLink]);
    }

    public function update(UpdateFooterLinkRequest $request, FooterLink $footerLink): RedirectResponse
    {
        $footerLink->update($this->payload($request->validated()));
        Cache::forget('footer_links_active_ordered');
        return redirect()->route('admin.footer-links.index')->with('success', 'Record updated successfully.');
    }

    public function destroy(FooterLink $footerLink): RedirectResponse
    {
        $footerLink->delete();
        Cache::forget('footer_links_active_ordered');
        return back()->with('success', 'Record deleted successfully.');
    }

    private function payload(array $data): array
    {
        $data['status'] = (bool) ($data['status'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        return $data;
    }
}
