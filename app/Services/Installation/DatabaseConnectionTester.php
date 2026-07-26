<?php

namespace App\Services\Installation;

use Illuminate\Support\Facades\DB;
use Throwable;

class DatabaseConnectionTester
{
    public function test(array $credentials): array
    {
        if (($credentials['driver'] ?? 'mysql') !== 'mysql') {
            return ['passed' => false, 'category' => 'unsupported_driver', 'message' => 'Only MySQL and MariaDB are supported.'];
        }
        $name = 'installer_probe';
        config(["database.connections.$name" => ['driver' => 'mysql', 'host' => $credentials['host'], 'port' => $credentials['port'], 'database' => $credentials['database'], 'username' => $credentials['username'], 'password' => $credentials['password'], 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'prefix' => '', 'strict' => true, 'options' => [\PDO::ATTR_TIMEOUT => 5]]]);
        try {
            DB::connection($name)->getPdo();

            return ['passed' => true, 'category' => 'connected', 'message' => 'Database connection succeeded.'];
        } catch (Throwable $e) {
            $message = strtolower($e->getMessage());
            $category = str_contains($message, 'access denied') ? 'authentication_failed' : (str_contains($message, 'unknown database') ? 'database_missing' : (str_contains($message, 'permission') ? 'permission_denied' : 'host_unreachable'));

            return ['passed' => false, 'category' => $category, 'message' => match ($category) {
                'authentication_failed' => 'Database authentication failed.', 'database_missing' => 'The database does not exist.', 'permission_denied' => 'The database user lacks required permission.', default => 'The database server could not be reached.'
            }];
        } finally {
            DB::purge($name);
            config()->offsetUnset("database.connections.$name");
        }
    }
}
