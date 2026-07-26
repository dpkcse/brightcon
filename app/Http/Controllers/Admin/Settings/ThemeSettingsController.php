<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Contracts\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ThemeSettingsRequest;
use App\Models\ThemeSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ThemeSettingsController extends Controller
{
    public function edit(): View
    {
        $setting = ThemeSetting::query()->firstOrCreate([], ['primary_color' => '#d80d4c', 'secondary_color' => '#ffffff']);

        return view('admin.pages.settings.theme', compact('setting'));
    }

    public function update(ThemeSettingsRequest $request, SettingsRepositoryInterface $settings): RedirectResponse
    {
        $setting = ThemeSetting::query()->firstOrCreate([], ['primary_color' => '#d80d4c', 'secondary_color' => '#ffffff']);
        $setting->update($request->validated());
        $settings->forgetThemeCache();

        return back()->with('success', 'Theme settings updated successfully.');
    }
}
