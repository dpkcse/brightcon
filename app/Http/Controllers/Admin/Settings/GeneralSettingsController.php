<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GeneralSettingsRequest;
use App\Models\SiteSetting;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class GeneralSettingsController extends Controller
{
    public function edit(): View
    {
        $setting = SiteSetting::query()->firstOrCreate([], ['company_name' => 'BrightCon']);
        return view('admin.pages.settings.general', compact('setting'));
    }

    public function update(GeneralSettingsRequest $request, FileUploadService $uploader): RedirectResponse
    {
        $setting = SiteSetting::query()->firstOrCreate([], ['company_name' => 'BrightCon']);
        $data = $request->safe()->except(['logo', 'favicon', 'profile_pdf']);
        $data['logo'] = $uploader->replace($request->file('logo'), $setting->logo, 'uploads/site/logo');
        $data['favicon'] = $uploader->replace($request->file('favicon'), $setting->favicon, 'uploads/site/favicon');
        $data['profile_pdf'] = $uploader->replace($request->file('profile_pdf'), $setting->profile_pdf, 'uploads/site/profile');
        $setting->update($data);
        Cache::forget('site_settings');
        return back()->with('success', 'General settings updated successfully.');
    }
}
