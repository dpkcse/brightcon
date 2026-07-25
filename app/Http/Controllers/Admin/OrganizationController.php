<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationRequest;
use App\Http\Requests\Admin\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.organizations.index', ['items' => Organization::query()->ordered()->paginate(15)]);
    }

    public function create(): View
    {
        return view('admin.pages.organizations.form', ['item' => new Organization]);
    }

    public function edit(Organization $organization): View
    {
        return view('admin.pages.organizations.form', ['item' => $organization]);
    }

    public function store(StoreOrganizationRequest $request, FileUploadService $uploader): RedirectResponse
    {
        $data = $this->payload($request->validated());
        $data['logo'] = $uploader->replace($request->file('logo'), null, 'uploads/organizations');
        Organization::create($data);
        $this->clearCache();

        return redirect()->route('admin.organizations.index')->with('success', 'Organization created successfully.');
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization, FileUploadService $uploader): RedirectResponse
    {
        $data = $this->payload($request->validated());
        $data['logo'] = $uploader->replace($request->file('logo'), $organization->logo, 'uploads/organizations');
        $organization->update($data);
        $this->clearCache();

        return redirect()->route('admin.organizations.index')->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();
        $this->clearCache();

        return back()->with('success', 'Organization deleted successfully.');
    }

    private function payload(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['is_featured'] = (bool) ($data['is_featured'] ?? false);
        $data['display_order'] = (int) ($data['display_order'] ?? 0);

        return $data;
    }

    private function clearCache(): void
    {
        Cache::forget('homepage_organizations_featured');
    }
}
