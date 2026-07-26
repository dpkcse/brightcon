<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Services\FileUploadService;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $r): View
    {
        $items = Equipment::query()->when($r->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$r->search.'%'))->ordered()->paginate(15);

        return view('admin.pages.equipment.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.pages.equipment.form', ['item' => new Equipment]);
    }

    public function store(Request $r, FileUploadService $u): RedirectResponse
    {
        $d = $this->data($r);
        $d['image_path'] = $u->replace($r->file('image'), null, 'uploads/equipment');
        Equipment::create($this->cast($d));

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment created.');
    }

    public function edit(Equipment $equipment): View
    {
        return view('admin.pages.equipment.form', ['item' => $equipment]);
    }

    public function update(Request $r, Equipment $equipment, FileUploadService $u): RedirectResponse
    {
        $d = $this->data($r, $equipment);
        $d['image_path'] = $u->replace($r->file('image'), $equipment->image_path, 'uploads/equipment');
        $equipment->update($this->cast($d));

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment updated.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        $equipment->update(['status' => 'archived']);

        return back()->with('success', 'Equipment archived.');
    }

    private function data(Request $r, ?Equipment $e = null): array
    {
        return $r->validate(['name' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('equipment')->ignore($e)], 'category' => 'nullable|string|max:255', 'brand' => 'nullable|string|max:255', 'model_number' => 'nullable|string|max:100', 'capacity' => 'nullable|string|max:100', 'quantity' => 'nullable|integer|min:0', 'unit' => 'nullable|string|max:50', 'short_description' => 'nullable|string|max:1000', 'description' => 'nullable|string|max:10000', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', 'image_alt' => 'nullable|string|max:255', 'status' => 'required|in:draft,published,archived', 'is_featured' => 'nullable|boolean', 'display_order' => 'nullable|integer|min:0', 'published_at' => 'nullable|date']);
    }

    private function cast(array $d): array
    {
        $d['is_featured'] = (bool) ($d['is_featured'] ?? false);
        $d['display_order'] = (int) ($d['display_order'] ?? 0);

        return $d;
    }
}
