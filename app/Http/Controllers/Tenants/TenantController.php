<?php

namespace App\Http\Controllers\Tenants;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tenants = Tenant::all();
            return ApiResponse::generalResponse($tenants, "Tenants data", true);
        } catch (\Throwable $th) {
            $url = $request->getHost();
            Log::info("TENANT RETRIEVAL === [URL === $url] " . $th->getMessage());
            return ApiResponse::generalResponse(null, "Error occurred when retrieving tenants data", false);
        }
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "company_name"  => 'required|string',
            // "subdomain"     =>  [
            //     'required',
            //     'string',
            //     function ($attribute, $value, $fail) {
            //         $new_value = str_replace(" ", "", $value);
            //         if (Tenant::where('subdomain', $new_value)->exists()) {
            //             return $fail('This subdomain is already taken.');
            //         }
            //     }
            // ]
        ]);

        $db_name = str_replace(" ", "_", $request->company_name);
        $subdomain = str_replace(" ", "", $request->company_name);
        $db_host = env("DB_HOST", 'NA');
        
        $tenant = new Tenant();

        if ($tenant->verifySubdomain($subdomain)) {
            return ApiResponse::generalResponse($request->all(), "This company name has been taken", false);
        }


        if ($validator->fails()) {
            $message = $validator->errors()->first();
            return ApiResponse::generalResponse(null, $message, false);
        }else {
            return ApiResponse::generalResponse($request->all(), "In-progress", true);
        }
    }
}
