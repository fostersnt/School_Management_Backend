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
        // Get the tenant ID from the argument
        $tenantId = $this->argument('tenantId');

        // Find the tenant by ID (or any other identifier you use)
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant not found.");
            return;
        }

        // Set the database connection for this tenant
        $this->switchDatabaseConnection($tenant);

        // Run migrations for this tenant's database
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
}
