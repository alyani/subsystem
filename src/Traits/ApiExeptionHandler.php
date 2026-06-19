<?php

namespace Alyani\Subsystem\Traits;

use Throwable;

/**
 * Api exception handler Trait
 *
 * by default, all response are in msgpack format
 * to change the default format just overwrite apiResponseDefaultFormat() method
 *
 * but to change some of the requests add below var
 * public $apiResposneFormat = []
 *
 */
trait ApiExeptionHandler
{
    use ApiResponse;

    /**
     * Handle api exeptions
     */
    private function handleApiException($request, Throwable $exception)
    {
        //  check if response format is set in handler
        $responseFormat = null;
        if ( !empty($this->apiResposneFormat)) {
            foreach ($this->apiResposneFormat as $uri => $format) {
                if ($request->is(trim($uri, '/'))) {
                    $responseFormat = $format;
                    break;
                }
            }
        }

        $exception = $this->prepareException($exception);


        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->error(0, null, 401, $responseFormat);
        }

        $exception = $this->prepareException($exception);
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->error(1, null, 401, $responseFormat);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $errors = $exception->errors();

            $errorData = [];
            foreach ($errors as $field => $messages) {
                $errorData[] = [
                    'field' => $field,
                    'message' => array_shift($messages),
                ];
            }

            return $this->errorValidate($errorData);
        }

        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            if (method_exists($exception, 'getStatusCode')) {
                return $this->error(2, $exception->getMessage(), $exception->getStatusCode(), $responseFormat);
            } else {
                return $this->error(3, $exception->getMessage(), 500, $responseFormat);
            }
        }

        if ($exception instanceof \Illuminate\Http\Exceptions\HttpResponseException) {
            return $this->error(4, $exception->getMessage(), 500, $responseFormat);
        }

        if ($exception instanceof \Alyani\Subsystem\Exceptions\CustomApiRequestException) {
            return $this->error($exception->getMessage(), null, $exception->getCode(), $responseFormat);
        }

        if ($exception instanceof \Alyani\Subsystem\Exceptions\UnauthorizedException) {
            return $this->error($exception->getMessage(), null, $exception->getCode(), $responseFormat);
        }

        return $this->error(500, null, 500, $responseFormat);
    }
}
