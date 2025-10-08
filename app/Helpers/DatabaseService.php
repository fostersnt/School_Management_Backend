<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseService
{
    public static function runTenantSpecificMigration($db_name)
    {
        $status_1 = self::checkAndCreateDatabase($db_name);
        if ($status_1['success'] === true) {
            $status_2 = self::changeDbConnection($db_name);
            if ($status_2['success'] === true) {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--force' => true,
                ]);
            } else {
                return $status_2;
            }
        } else {
            return $status_1;
        }
    }

    private static function changeDbConnection($db_name)
    {
        $db_host        =   env("DB_HOST", 'N/A');
        $db_username    =   env('DB_USERNAME', 'N/A');
        $db_password    =   env("DB_PASSWORD");

        Config::set('database.connections.tenant', [
            'driver'   => 'mysql',
            'host'     => $db_host,
            'database' => $db_name,
            'username' => $db_username,
            'password' => $db_password,
        ]);

        try {
            DB::connection('tenant')->getPdo();
            return [
                "success" => true,
                "message" => "Database connection successful for database: {$db_name}"
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Database connection failed for database: {$db_name} - " . $e->getMessage()
            ];
        }
    }

    private static function checkAndCreateDatabase($db_name)
    {
        try {
            $connection = DB::connection('mysql');

            $databaseExists = $connection->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$db_name]);

            if (empty($databaseExists)) {
                Log::info("Database {$db_name} does not exist. Creating...");

                $connection->statement("CREATE DATABASE IF NOT EXISTS `{$db_name}`");

                Log::info("Database {$db_name} created.");
            }

            return [
                "success" => true,
                "message" => "Database created for database: {$db_name}"
            ];
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => "Database creation failed for database: {$db_name} - " . $e->getMessage()
            ];
        }
    }
}
