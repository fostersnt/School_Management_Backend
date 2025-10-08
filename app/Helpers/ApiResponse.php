<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse {
    public static function generalResponse($data, $message, $is_success): JsonResponse
    {
        $status = $is_success === true ? 'success' : 'failed';

        $resp_data = [
            "request_status"    => $status,
            "message"           => $message,
            "data"              => $data
        ];

        return response()->json($resp_data, 200);
    }

    public static function badRequestResponse($message): JsonResponse
    {
        $resp_data = [
            "request_status"    => "failed",
            "message"           => $message,
        ];

        return response()->json($resp_data, 400);
    }

    public static function unauthorizedRequestResponse(): JsonResponse
    {
        $resp_data = [
            "request_status"    => "failed",
            "message"           => "Unauthorized",
        ];

        return response()->json($resp_data, 401);
    }
}