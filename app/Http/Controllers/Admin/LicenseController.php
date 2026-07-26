<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Licensing\LicenseManager;
use App\Services\Licensing\LicensePolicyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LicenseController extends Controller
{
    public function index(LicenseManager $licenses, LicensePolicyService $policy): View
    {
        return view('admin.pages.license.index', [
            'status' => $licenses->status()->value,
            'decision' => $policy->decisionFor('license.view'),
            'providers' => config('licensing.providers'),
        ]);
    }

    public function activate(Request $request, LicenseManager $licenses): RedirectResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'string', 'max:64'],
            'credential' => ['required', 'string', 'max:100000'],
        ]);
        $host = (string) parse_url((string) config('app.url'), PHP_URL_HOST);
        $decision = $licenses->activate($validated['provider'], $validated['credential'], $host);

        return back()->with($decision->permitsUse() ? 'success' : 'error', $decision->permitsUse()
            ? 'License activated.'
            : ($decision->reason ?: 'The license could not be activated. Review the configuration and try again.'));
    }
}
