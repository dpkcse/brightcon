<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Contracts\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GeneralSettingsRequest;
use App\Models\SiteSetting;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GeneralSettingsController extends Controller
{
    public function edit(): View
    {
        $setting = SiteSetting::query()->firstOrCreate([], ['company_name' => config('cms.defaults.company_name')]);

        return view('admin.pages.settings.general', compact('setting'));
    }

    public function update(GeneralSettingsRequest $request, FileUploadService $uploader, SettingsRepositoryInterface $settings): RedirectResponse
    {
        $setting = SiteSetting::query()->firstOrCreate([], ['company_name' => config('cms.defaults.company_name')]);
        $data = $request->safe()->except(['logo', 'favicon', 'profile_pdf', 'dark_logo_path', 'light_logo_path', 'open_graph_image_path', 'twitter_card_image_path']);
        $data['map_zoom'] ??= 15;
        $data['logo'] = $uploader->replace($request->file('logo'), $setting->logo, 'uploads/site/logo');
        $data['favicon'] = $uploader->replace($request->file('favicon'), $setting->favicon, 'uploads/site/favicon');
        $data['profile_pdf'] = $uploader->replace($request->file('profile_pdf'), $setting->profile_pdf, 'uploads/site/profile');
        foreach (['dark_logo_path' => 'logos/dark', 'light_logo_path' => 'logos/light', 'open_graph_image_path' => 'seo/open-graph', 'twitter_card_image_path' => 'seo/twitter'] as $field => $folder) {
            $data[$field] = $uploader->replace($request->file($field), $setting->$field, 'uploads/site/'.$folder);
        }
        $setting->update($data);
        $settings->forgetSiteCache();

        return back()->with('success', 'General settings updated successfully.');
    }
}
