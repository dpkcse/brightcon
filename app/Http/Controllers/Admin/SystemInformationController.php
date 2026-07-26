<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Licensing\LicenseManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SystemInformationController extends Controller
{
    public function __invoke(SettingsRepositoryInterface $settings, LicenseManager $licenses): View
    {
        $auditExit = Artisan::call('commercial:audit', ['--no-git' => true]);

        return view('admin.pages.system-information', ['information' => [
            'Product name' => $settings->string('product_name') ?: config('cms.product.name'),
            'Product description' => config('cms.product.description'),
            'Edition' => config('cms.product.edition'),
            'Product version' => $settings->string('installed_version') ?: $settings->string('product_version') ?: config('cms.product.version'),
            'Laravel version' => app()->version(), 'PHP version' => PHP_VERSION,
            'Database driver' => config('database.default'), 'Cache driver' => config('cache.default'),
            'Session driver' => config('session.driver'), 'Queue driver' => config('queue.default'),
            'Filesystem driver' => config('filesystems.default'), 'Environment' => app()->environment(),
            'Debug status' => config('app.debug') ? 'Enabled' : 'Disabled',
            'Installation status' => $settings->site()->installation_completed_at ? 'Prepared / recorded' : 'Not recorded',
            'License status' => str_replace('_', ' ', ucfirst($licenses->status()->value)),
            'Storage link' => is_link(public_path('storage')) ? 'Linked' : 'Not linked',
            'Storage writable' => is_writable(storage_path()) ? 'Yes' : 'No',
            'Cache writable' => is_writable(storage_path('framework/cache')) ? 'Yes' : 'No',
            'Commercial audit' => $auditExit === 0 ? 'Passed' : 'Review required',
        ]]);
    }
}
