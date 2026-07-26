<?php

namespace App\Http\Controllers;

use App\Services\Installation\InstallationManager;
use App\Services\Installation\PermissionChecker;
use App\Services\Installation\RequirementChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use RuntimeException;

class InstallerController extends Controller
{
    public function welcome(): View
    {
        return view('installer.welcome');
    }

    public function requirements(RequirementChecker $checker): View
    {
        return view('installer.requirements', ['items' => $checker->check(), 'passes' => $checker->passes()]);
    }

    public function permissions(PermissionChecker $checker): View
    {
        return view('installer.permissions', ['items' => $checker->check(true), 'storageStatus' => $checker->storageLinkStatus()]);
    }

    public function form(): View
    {
        return view('installer.form');
    }

    public function review(Request $request): View
    {
        $safe = $request->validate($this->rules(false));
        $safe['db_password'] = $safe['db_password'] ?? '';
        session(['installer.safe' => collect($safe)->except(['db_password', 'admin_password', 'admin_password_confirmation'])->all()]);

        return view('installer.review', ['summary' => session('installer.safe')]);
    }

    public function process(Request $request, InstallationManager $manager): RedirectResponse
    {
        $input = $request->validate($this->rules(true));
        $input['db_password'] = $input['db_password'] ?? '';
        abort_unless($request->boolean('confirm'), 422, 'Explicit confirmation is required.');
        try {
            $result = $manager->install($input, is_file(base_path('.env')) && $request->boolean('approve_env_update'));
            session()->forget('installer.safe');
            session(['installer.complete' => $result]);

            return redirect()->route('install.complete');
        } catch (RuntimeException $e) {
            return back()->withErrors(['installation' => $e->getMessage()])->withInput($request->except(['db_password', 'admin_password', 'admin_password_confirmation']));
        }
    }

    public function complete(): View
    {
        abort_unless(session()->has('installer.complete'), 404);

        return view('installer.complete', ['result' => session('installer.complete')]);
    }

    private function rules(bool $processing): array
    {
        return ['app_name' => ['required', 'string', 'max:100'], 'app_url' => ['required', 'url:http,https', 'max:255'], 'db_host' => ['required', 'string', 'max:255'], 'db_port' => ['required', 'integer', 'between:1,65535'], 'db_name' => ['required', 'regex:/^[A-Za-z0-9_$-]+$/', 'max:64'], 'db_user' => ['required', 'string', 'max:128'], 'db_password' => ['nullable', 'string', 'max:1024'], 'admin_name' => ['required', 'string', 'max:255'], 'admin_email' => ['required', 'email:rfc', 'max:255'], 'admin_password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()], 'seed' => ['required', 'in:clean,demo'], 'storage_link' => ['nullable', 'boolean'], 'approve_env_update' => ['nullable', 'boolean'], ...($processing ? ['confirm' => ['accepted']] : [])];
    }
}
