<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TenantIdentification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $this->getSubdomainFromHost($request->getHost());

        if (!$subdomain) {
            abort(404, 'Tenant not found');
        }

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        Log::info("TENANT === " . json_encode($tenant));

        if (!$tenant) {
            // return response('Tenant not found', 404);
            abort(404, 'Tenant not found');
        }

        $connection_status = $this->switchDatabaseConnection($tenant);

        if ($connection_status === true) {
            return $next($request);
        } else {
            abort(401);
        }
    }

    private function getSubdomainFromHost($host)
    {
        $hostParts = explode('.', $host);

        if (count($hostParts) > 2) {
            return $hostParts[0];
        }

        return null;
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

            Log::info("Successfully connected to tenant database for: " . $tenant->subdomain);

            return true;
        } catch (\Exception $e) {
            Log::error("Database connection failed for tenant: " . json_encode($tenant) . " - " . $e->getMessage());

            return false;
        }
    }
}
