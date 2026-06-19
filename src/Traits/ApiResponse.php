<?php

namespace Alyani\Subsystem\Traits;

use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * Return a raw response (JSON).
     *
     * @param array $data
     * @param int $httpCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response(array $data, int $httpCode = Response::HTTP_OK)
    {
        $default = [
            'status' => 'success',
            'data' => [],
            'error' => null,
        ];

        return response()->json(array_merge($default, $data), $httpCode);
    }

    /**
     * Return a SUCCESS response (JSON).
     *
     * @param array $data
     * @param int $httpCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success(array $data = [], int $httpCode = Response::HTTP_OK)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'error' => null,
        ], $httpCode);
    }

    /**
     * Return an ERROR response (JSON).
     *
     * @param array|string|null $error
     * @param string|null $message
     * @param int $httpCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($error = null, ?string $message = null, int $httpCode = Response::HTTP_OK)
    {
        // Use default HTTP status text if no custom message is provided
        $defaultMessage = Response::$statusTexts[$httpCode] ?? 'Unknown Error';
        $message = $message ?: st($defaultMessage);

        $errorData = is_array($error) ? $error : [
            'errorCode' => $error,
            'message' => $message,
        ];

        return response()->json([
            'status' => 'error',
            'data' => $errorData,
            'error' => null,
        ], $httpCode);
    }

    protected function errorValidate($error = null, int $httpCode = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        return response()->json([
            'status' => 'error',
            'data' => [],
            'error' => $error,
        ], $httpCode);
    }
}