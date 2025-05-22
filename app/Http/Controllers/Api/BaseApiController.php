<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Exception;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class BaseApiController extends Controller
{
    /**
     * Constructor init
     */
    public function __construct() {}

    /**
     * Function to return Json response.
     *
     * @param array|null $data
     * @return JsonResponse
     */
    public function correctResponse(array $data = null): JsonResponse
    {
        // Create the response array
        $response = [
            'data' => $data,
        ];

        return new JsonResponse(data: $response, status: Response::HTTP_OK);
    }

    /**
     * Function to return Json response with custom error code and message.
     *
     * @param int $errorCode
     * @param string|null $message
     * @return JsonResponse
     */
    public function incorrectResponse(int $errorCode = 1, string $message = null): JsonResponse
    {
        // Set the response lang
        $response_locale = $lang ?? App::getLocale();

        // Set locale
        App::setLocale($response_locale);

        // If no custom message is given return the default error message for the given error code
        if (empty($message)) {
            $response_message = trans(key: 'errors.' . $errorCode);
        } else {
            $response_message = $message;
        }

        // Create the response array
        $response = [
            'error' => [
                'errorCode' => $errorCode,
                'message' => $response_message,
            ]
        ];

        return new JsonResponse(data: $response, status: Response::HTTP_OK);
    }

    /**
     * Function to log incorrect response
     *
     * @param Exception|Throwable $exception
     * @return void
     */
    public function logIncorrectResponse(Exception|Throwable $exception): void
    {
        // Log the response
        Log::channel('app-errors')->error(
            get_class($exception) . PHP_EOL .
                'Error (' . $exception->getCode() . '): ' . $exception->getMessage() . PHP_EOL .
                'Line: ' . $exception->getLine() . PHP_EOL .
                'File: ' . $exception->getFile() . PHP_EOL .
                'Trace:' . PHP_EOL . $exception->getTraceAsString()
        );
    }
}
