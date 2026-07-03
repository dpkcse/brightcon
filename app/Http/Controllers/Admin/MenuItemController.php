<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMenuItemRequest;
use App\Http\Requests\Admin\UpdateMenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
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
        return view('admin.pages.menu-items.form', ['item' => new MenuItem()]);
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        MenuItem::create($this->payload($request->validated()));
        Cache::forget('menu_items_active_ordered');
        return redirect()->route('admin.menu-items.index')->with('success', 'Record created successfully.');
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('admin.pages.menu-items.form', ['item' => $menuItem]);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update($this->payload($request->validated()));
        Cache::forget('menu_items_active_ordered');
        return redirect()->route('admin.menu-items.index')->with('success', 'Record updated successfully.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();
        Cache::forget('menu_items_active_ordered');
        return back()->with('success', 'Record deleted successfully.');
    }

    private function payload(array $data): array
    {
        $data['status'] = (bool) ($data['status'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        return $data;
    }
}
