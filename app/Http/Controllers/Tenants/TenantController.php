<?php

namespace App\Http\Controllers\Tenants;

use App\Helpers\ApiResponse;
use App\Helpers\DatabaseService;
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

        try {
            if ($validator->fails()) {
                $message = $validator->errors()->first();
                return ApiResponse::generalResponse(null, $message, false);
            } else {
                $db_name = str_replace(" ", "_", $request->company_name);
                $subdomain = str_replace(" ", "", $request->company_name);

                $tenant_obj = new Tenant();

                if ($tenant_obj->verifySubdomain($subdomain)) {
                    return ApiResponse::generalResponse($request->all(), "This company name has been taken", false);
                }

                $new_tenant = Tenant::query()->create([
                    "company_name"  =>  "",
                    "db_name"       =>  "",
                    "subdomain"     =>  ""
                ]);

                $result = DatabaseService::runTenantSpecificMigration($db_name);

                Log::info("MIGRATION RESULT FOR DATABASE [$db_name] === " . json_encode($result));

                if ($result['success'] === true) {
                    return ApiResponse::generalResponse($new_tenant, "New tenant created", true);
                } else {
                    $new_tenant->query()->delete();
                    return ApiResponse::generalResponse(null, "Unable to create new tenant", true);
                }
            }
        } catch (\Throwable $th) {
            Log::info("MIGRATION ERROR FOR DATABASE [$db_name] === " . $th->getMessage());
            return ApiResponse::generalResponse(null, "Unable to create new tenant", true);
        }
    }
}
