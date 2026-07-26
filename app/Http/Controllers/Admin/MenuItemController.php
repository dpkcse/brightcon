<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $items = MenuItem::query()->ordered()->paginate(15);

        return view('admin.pages.menu-items.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.pages.menu-items.form', ['item' => new MenuItem, 'parents' => MenuItem::query()->whereNull('parent_id')->ordered()->get(), 'pages' => Page::query()->published()->ordered()->get()]);
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $this->validateStructure($request->validated());
        MenuItem::create($this->payload($request->validated()));
        Cache::forget('menu_items_active_ordered');
        Cache::forget(config('cms.cache.frontend'));
        Cache::forget(config('cms.cache.header'));

        return redirect()->route('admin.menu-items.index')->with('success', 'Record created successfully.');
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('admin.pages.menu-items.form', ['item' => $menuItem, 'parents' => MenuItem::query()->whereNull('parent_id')->whereKeyNot($menuItem)->ordered()->get(), 'pages' => Page::query()->published()->ordered()->get()]);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $this->validateStructure($request->validated(), $menuItem);
        $menuItem->update($this->payload($request->validated()));
        Cache::forget('menu_items_active_ordered');
        Cache::forget(config('cms.cache.frontend'));
        Cache::forget(config('cms.cache.header'));

        return redirect()->route('admin.menu-items.index')->with('success', 'Record updated successfully.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        if ($menuItem->children()->exists()) {
            return back()->withErrors(['menu' => 'Move child items before deleting this parent.']);
        } $menuItem->delete();
        Cache::forget('menu_items_active_ordered');
        Cache::forget(config('cms.cache.frontend'));
        Cache::forget(config('cms.cache.header'));

        return back()->with('success', 'Record deleted successfully.');
    }

    private function validateStructure(array $data, ?MenuItem $item = null): void
    {
        if ($item && (int) ($data['parent_id'] ?? 0) === $item->id) {
            throw ValidationException::withMessages(['parent_id' => 'An item cannot be its own parent.']);
        }
        if (! empty($data['parent_id'])) {
            $parent = MenuItem::find($data['parent_id']);
            if ($parent?->parent_id || $parent?->menu_location !== $data['menu_location']) {
                throw ValidationException::withMessages(['parent_id' => 'Choose a root item in the same menu location.']);
            }
        }
        if (($data['link_type'] ?? 'legacy') === 'route' && (! Route::has($data['route_name'] ?? '') || str_starts_with($data['route_name'] ?? '', 'admin.') || str_starts_with($data['route_name'] ?? '', 'install.'))) {
            throw ValidationException::withMessages(['route_name' => 'Choose a safe public route.']);
        }
    }

    private function payload(array $data): array
    {
        $data['url'] = $data['url'] ?? '#';
        $data['status'] = (bool) ($data['status'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
