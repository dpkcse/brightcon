<?php

namespace App\Services\Installation;

use App\Models\SiteSetting;
use App\Services\Administration\AdminCreator;
use Database\Seeders\Demo\DemoContentSeeder;
use Database\Seeders\EssentialSystemSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class InstallationManager
{
    public function __construct(private InstallationStateService $state, private RequirementChecker $requirements, private PermissionChecker $permissions, private EnvironmentWriter $environment, private DatabaseConnectionTester $database, private AdminCreator $admins) {}

    public function install(array $input, bool $allowExistingEnvironment = false): array
    {
        if (! in_array($input['seed'], ['clean', 'demo'], true)) {
            throw new RuntimeException('The seed mode must be clean or demo.');
        }
        $handle = fopen(storage_path('app/.installation.lock'), 'c+');
        if (! $handle || ! flock($handle, LOCK_EX | LOCK_NB)) {
            throw new RuntimeException('Another installation is already running.');
        }
        try {
            if (! $this->state->canRunInstaller()) {
                throw new RuntimeException('Installation is locked or requires manual recovery.');
            }
            if (! $this->requirements->passes()) {
                throw new RuntimeException('Blocking server requirements failed.');
            }
            if (! $this->permissions->passes()) {
                throw new RuntimeException('Required directories are not writable.');
            }
            $db = ['driver' => 'mysql', 'host' => $input['db_host'], 'port' => $input['db_port'], 'database' => $input['db_name'], 'username' => $input['db_user'], 'password' => (string) ($input['db_password'] ?? '')];
            $test = $this->database->test($db);
            if (! $test['passed']) {
                throw new RuntimeException($test['message']);
            }
            $key = config('app.key');
            if (! $this->environment->validKey($key)) {
                $key = $this->environment->generateKey();
            }
            file_put_contents(storage_path('app/.installation-partial'), now()->toIso8601String(), LOCK_EX);
            $this->environment->write(['APP_NAME' => $input['app_name'], 'APP_ENV' => 'production', 'APP_DEBUG' => 'false', 'APP_URL' => $input['app_url'], 'APP_KEY' => $key, 'DB_CONNECTION' => 'mysql', 'DB_HOST' => $db['host'], 'DB_PORT' => $db['port'], 'DB_DATABASE' => $db['database'], 'DB_USERNAME' => $db['username'], 'DB_PASSWORD' => $db['password'], 'CACHE_STORE' => 'file', 'SESSION_DRIVER' => 'file', 'QUEUE_CONNECTION' => 'sync', 'FILESYSTEM_DISK' => 'public'], $allowExistingEnvironment);
            config(['app.key' => $key, 'app.name' => $input['app_name'], 'app.url' => $input['app_url'], 'database.default' => 'mysql', 'database.connections.mysql.host' => $db['host'], 'database.connections.mysql.port' => $db['port'], 'database.connections.mysql.database' => $db['database'], 'database.connections.mysql.username' => $db['username'], 'database.connections.mysql.password' => $db['password'], 'cache.default' => 'file', 'session.driver' => 'file', 'queue.default' => 'sync']);
            DB::purge('mysql');
            DB::reconnect('mysql');
            if (Schema::hasTable('site_settings') && SiteSetting::query()->exists() && $this->state->state()->value !== 'partially_installed') {
                throw new RuntimeException('Existing CMS tables require recovery review; no records were changed.');
            }
            if (Artisan::call('migrate', ['--force' => true, '--no-interaction' => true]) !== 0) {
                throw new RuntimeException('Database migration failed.');
            }
            Artisan::call('db:seed', ['--class' => EssentialSystemSeeder::class, '--force' => true]);
            if ($input['seed'] === 'demo') {
                Artisan::call('db:seed', ['--class' => DemoContentSeeder::class, '--force' => true]);
            }
            $this->admins->create(['name' => $input['admin_name'], 'email' => $input['admin_email'], 'password' => $input['admin_password']]);
            $settings = SiteSetting::query()->firstOrFail();
            $settings->forceFill(['installation_completed_at' => now(), 'installed_version' => config('cms.product.version')])->save();
            $storage = $input['storage_link'] ?? true;
            $storageStatus = $this->permissions->storageLinkStatus();
            if ($storage && $storageStatus === 'missing') {
                Artisan::call('storage:link');
                $storageStatus = $this->permissions->storageLinkStatus();
            }
            $this->state->markInstalled();

            return ['version' => config('cms.product.version'), 'admin_email' => $input['admin_email'], 'app_name' => $input['app_name'], 'seed' => $input['seed'], 'storage_status' => $storageStatus];
        } catch (Throwable $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            $reference = (string) Str::uuid();
            Log::error('Installation step failed.', ['reference' => $reference, 'exception' => $e::class]);
            throw new RuntimeException("Installation could not complete. Support reference: {$reference}", 0, $e);
        } finally {
            if (is_resource($handle)) {
                flock($handle, LOCK_UN);
                fclose($handle);
            }
        }
    }
}
