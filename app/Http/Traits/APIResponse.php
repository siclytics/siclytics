<?php

namespace App\Http\Traits;

trait APIResponse {

    public function success($result, $message, $code = 200){
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function error($error, $errorMessages = [], $code = 404){
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages))
            $response['data'] = $errorMessages;

        return response()->json($response, $code);
    }
}