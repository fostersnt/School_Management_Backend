<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MigrateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate {tenantId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for a specific tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenantId');

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant not found.");
            exit();
        }

        $this->checkAndCreateDatabase($tenant);

        $this->switchDatabaseConnection($tenant);

        $this->info("Running migrations for tenant: {$tenant->subdomain}");

        $this->call('migrate', [
            '--database' => 'tenant',
            '--force' => true,
        ]);
    }

    private function switchDatabaseConnection($tenant)
    {
        Config::set('database.connections.tenant', [
            'driver'   => 'mysql',
            'host'     => $tenant->db_host,
            'database' => $tenant->db_name,
            'username' => $tenant->db_username,
            'password' => $tenant->db_password,
        ]);

        try {
            DB::connection('tenant')->getPdo();
            $this->info("Database connection successful for tenant: " . json_encode($tenant));
        } catch (\Exception $e) {
            $this->error("Database connection failed for tenant: {$tenant->subdomain} - " . $e->getMessage());
            exit;
        }
    }

    private function checkAndCreateDatabase($tenant)
    {
        try {
            $connection = DB::connection('mysql');

            $databaseExists = $connection->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$tenant->db_name]);

            if (empty($databaseExists)) {
                $this->warn("Database for tenant {$tenant->subdomain} does not exist. Creating...");

                $connection->statement("CREATE DATABASE IF NOT EXISTS `{$tenant->db_name}`");

                $this->info("Database for tenant {$tenant->subdomain} created.");
            }
        } catch (\Exception $e) {
            $this->error("Error checking/creating database: " . $e->getMessage());
            exit();
        }
    }
}
