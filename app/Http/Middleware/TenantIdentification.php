<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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
            abort(404, 'Tenant not found');
        Log::info("TENANT NOT FOUND === " . json_encode($tenant));
        }

        $this->switchDatabaseConnection($tenant);

        Log::info("TENANT SWITCHED === " . json_encode($tenant));

        return $next($request);
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
    }
}
