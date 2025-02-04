<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ApiService
{
    public static function response($data, $statusCode = 200)
    {
        $response = [];
        $locale = App::getLocale();

        if ($statusCode >= 200 && $statusCode < 300) {
            $response = $data ?: ['message' => __('messages.success', [], $locale)];
        } elseif ($statusCode >= 400 && $statusCode < 500) {
            $response['message'] = __('messages.error', [], $locale);
            if ($statusCode == 404) {
                $response['message'] = __('messages.resource_not_found', [], $locale);
            }
            $response['errors'] = $data;
        } elseif ($statusCode >= 500) {
            if (config('app.debug')) {
                $response['error'] = $data;
            } else {
                $response['message'] = __('messages.server_error', [], $locale);
            }
            Log::error($data);
        }

        return response()->json($response, $statusCode);
    }
}
