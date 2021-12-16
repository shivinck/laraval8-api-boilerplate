<?php

namespace App\Helpers;

class Response {

    public static function sendResponse($data, $message, $code=200) {
    	$response = [
            'success' => true,
            'data'    => $data,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
            'reference_key' => time(),
        ];
        return response()->json($response, $code);
    }

    public static function sendError($data, $message = [], $code = 417) {
    	$response = [
            'success' => false,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s'),
            'reference_key' => time(),
            'data' => [],
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $data;
        }
        return response()->json($response, $code);
    }

}
