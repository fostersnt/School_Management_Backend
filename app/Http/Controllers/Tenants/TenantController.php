<?php

namespace App\Http\Controllers\Tenants;

use App\Helpers\General;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenants = Tenant::all();
            return General::successRequestResponse($tenants, "Tenants data");
        } catch (\Throwable $th) {
            $url = $request->getHost();
            Log::info("TENANT RETRIEVAL === [URL === $url] " . $th->getMessage());
            return General::failedRequestResponse([], "Error occurred when retrieving tenants data");
        }
    }
}
