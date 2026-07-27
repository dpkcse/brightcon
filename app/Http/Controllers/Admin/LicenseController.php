<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Licensing\LicenseManager;
use App\Services\Licensing\LicensePolicyService;
use App\Services\Licensing\NaxasActivationService;
use App\Support\RuntimeDemoMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class LicenseController extends Controller
{
    public function index(LicenseManager $licenses, LicensePolicyService $policy, NaxasActivationService $portal): View
    {
        return view('admin.pages.license.index', [
            'status' => $licenses->status()->value,
            'decision' => $policy->decisionFor('license.view'),
            'providers' => config('licensing.providers'),
            'activationRequest' => $portal->current(),
            'installationUuid' => $licenses->installationId(),
            'normalizedDomain' => strtolower(rtrim((string) parse_url((string) config('app.url'), PHP_URL_HOST), '.')),
            'portalEnabled' => (bool) config('licensing.naxas_portal.enabled'),
            'verificationKeyConfigured' => filled(config('licensing.offline.public_key')),
            'currentActivation' => $licenses->current(),
        ]);
    }

    public function activate(Request $request, LicenseManager $licenses): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        $validated = $request->validate([
            'provider' => ['required', 'string', 'max:64'],
            'credential' => ['nullable', 'string', 'max:100000', 'required_without:license_file'],
            'license_file' => ['nullable', 'file', 'max:128', 'required_without:credential'],
        ]);
        $credential = isset($validated['license_file'])
            ? trim((string) file_get_contents($validated['license_file']->getRealPath()))
            : (string) $validated['credential'];
        $host = (string) parse_url((string) config('app.url'), PHP_URL_HOST);
        $decision = $licenses->activate($validated['provider'], $credential, $host);

        return back()->with($decision->permitsUse() ? 'success' : 'error', $decision->permitsUse()
            ? 'License activated.'
            : ($decision->reason ?: 'The license could not be activated. Review the configuration and try again.'));
    }

    public function requestActivation(NaxasActivationService $portal): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        try {
            $portal->create();

            return redirect()->route('admin.license.index')->with('success', 'Activation request created. Copy its one-time token before leaving this page.');
        } catch (\RuntimeException $exception) {
            return redirect()->route('admin.license.index')->with('error', $exception->getMessage());
        }
    }

    public function checkActivation(NaxasActivationService $portal): RedirectResponse
    {
        RuntimeDemoMode::abortIfProtected();
        try {
            return redirect()->route('admin.license.index')->with('success', $portal->check());
        } catch (\RuntimeException $exception) {
            return redirect()->route('admin.license.index')->with('error', $exception->getMessage());
        }
    }
}
