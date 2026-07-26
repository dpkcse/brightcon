<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competency;
use App\Services\FileUploadService;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompetencyController extends Controller
{
    public function index(Request $r): View
    {
        $items = Competency::query()->when($r->filled('search'), fn ($q) => $q->where('title', 'like', '%'.$r->search.'%'))->ordered()->paginate(15);

        return view('admin.pages.competencies.index', compact('items'));
    }

    public function create(): View
    {
        return view('admin.pages.competencies.form', ['item' => new Competency]);
    }

    public function store(Request $r, FileUploadService $u): RedirectResponse
    {
        $d = $this->data($r);
        $d['image_path'] = $u->replace($r->file('image'), null, 'uploads/competencies');
        Competency::create($this->cast($d));

        return redirect()->route('admin.competencies.index')->with('success', 'Competency created.');
    }

    public function edit(Competency $competency): View
    {
        return view('admin.pages.competencies.form', ['item' => $competency]);
    }

    public function update(Request $r, Competency $competency, FileUploadService $u): RedirectResponse
    {
        $d = $this->data($r, $competency);
        $d['image_path'] = $u->replace($r->file('image'), $competency->image_path, 'uploads/competencies');
        $competency->update($this->cast($d));

        return redirect()->route('admin.competencies.index')->with('success', 'Competency updated.');
    }

    public function destroy(Competency $competency): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        $competency->update(['status' => 'archived']);

        return back()->with('success', 'Competency archived.');
    }

    private function data(Request $r, ?Competency $e = null): array
    {
        return $r->validate(['title' => 'required|string|max:255', 'slug' => ['required', 'alpha_dash', 'max:255', Rule::unique('competencies')->ignore($e)], 'icon' => 'nullable|string|max:100', 'short_description' => 'nullable|string|max:1000', 'description' => 'nullable|string|max:10000', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096', 'image_alt' => 'nullable|string|max:255', 'status' => 'required|in:draft,published,archived', 'is_featured' => 'nullable|boolean', 'display_order' => 'nullable|integer|min:0', 'published_at' => 'nullable|date']);
    }

    private function cast(array $d): array
    {
        $d['is_featured'] = (bool) ($d['is_featured'] ?? false);
        $d['display_order'] = (int) ($d['display_order'] ?? 0);

        return $d;
    }
}
