<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\FileUploadService;
use App\Support\PageSlug;
use App\Support\RichContentSanitizer;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $r): View
    {
        $items = Page::query()->when($r->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$r->search.'%'))->when($r->filled('status'), fn ($q) => $q->where('status', $r->status))->ordered()->paginate(15)->withQueryString();

        return view('admin.pages.pages.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.pages.pages.form', ['item' => new Page]);
    }

    public function store(Request $r, FileUploadService $u, RichContentSanitizer $s): RedirectResponse
    {
        $d = $this->validateData($r);
        PageSlug::assertAllowed($d['slug'] = PageSlug::normalize($d['slug'] ?: $d['title']));
        $d['content'] = $s->sanitize($d['content'] ?? null);
        $d['featured_image_path'] = $u->replace($r->file('featured_image'), null, 'uploads/pages');
        $d['created_by'] = $d['updated_by'] = $r->user()->id;
        $this->booleans($d);
        Page::create($d);
        $this->clear();

        return redirect()->route('admin.pages.index')->with('success', 'Page created.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.pages.form', ['item' => $page]);
    }

    public function update(Request $r, Page $page, FileUploadService $u, RichContentSanitizer $s): RedirectResponse
    {
        $d = $this->validateData($r, $page);
        PageSlug::assertAllowed($d['slug'] = PageSlug::normalize($d['slug'] ?: $d['title']));
        $d['content'] = $s->sanitize($d['content'] ?? null);
        $d['featured_image_path'] = $u->replace($r->file('featured_image'), $page->featured_image_path, 'uploads/pages');
        $d['updated_by'] = $r->user()->id;
        $this->booleans($d);
        $page->update($d);
        $this->clear();

        return redirect()->route('admin.pages.index')->with('success', 'Page updated.');
    }

    public function destroy(Page $page, FileUploadService $u): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        if ($page->menuItems()->exists()) {
            return back()->withErrors(['page' => 'Archive this page or remove its menu references before deletion.']);
        }$page->delete();
        $this->clear();

        return back()->with('success', 'Page deleted.');
    }

    private function validateData(Request $r, ?Page $p = null): array
    {
        return $r->validate(['title' => 'required|string|max:255', 'slug' => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($p)], 'excerpt' => 'nullable|string|max:1000', 'content' => 'nullable|string|max:100000', 'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', 'featured_image_alt' => 'nullable|string|max:255', 'seo_title' => 'nullable|string|max:255', 'seo_description' => 'nullable|string|max:500', 'seo_keywords' => 'nullable|string|max:500', 'status' => 'required|in:draft,published,archived', 'is_featured' => 'nullable|boolean', 'display_order' => 'nullable|integer|min:0|max:65535', 'published_at' => 'nullable|date']);
    }

    private function booleans(array &$d): void
    {
        $d['is_featured'] = (bool) ($d['is_featured'] ?? false);
        $d['display_order'] = (int) ($d['display_order'] ?? 0);
    }

    private function clear(): void
    {
        Cache::forget('sitemap_pages_published');
        Cache::forget('menu_items_active_ordered');
    }
}
