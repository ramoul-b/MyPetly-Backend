<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Services\ApiService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        Log::error($exception);

        if ($exception instanceof AuthenticationException) {
            return ApiService::response([
                'message' => __('messages.unauthenticated'),
            ], 401);
        }

        if ($exception instanceof ValidationException) {
            return ApiService::response([
                'message' => __('messages.validation_failed'),
                'errors' => $exception->errors(),
            ], 422);
        }

        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $message = method_exists($exception, 'getMessage') ? $exception->getMessage() : Response::$statusTexts[$statusCode];

        return ApiService::response([
            'message' => $message,
        ], $statusCode);
    }
}
