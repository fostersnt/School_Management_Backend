<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class General {
    public static function successRequestResponse($data = [], $message = 'N/A'): JsonResponse
    {
        $resp_data = [
            "request_status"    => "success",
            "message"           => $message,
            "data"              => $data ?? []
        ];

        return response()->json($resp_data, 200);
    }

    public static function failedRequestResponse($data = [], $message = 'N/A'): JsonResponse
    {
        $resp_data = [
            "request_status"    => "failed",
            "message"           => $message,
            "data"              => $data ?? []
        ];

        return response()->json($resp_data, 200);
    }
}