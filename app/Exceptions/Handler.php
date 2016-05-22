<?php

namespace App\Exceptions;

use Exception;
use GoCanada\Popos\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        // debug for development. Showing plain error without any treatments
        if (env('EXCEPTION_HANDLER_NO_CATCH') == true) {
            $error['message']      = $e->getMessage();
            $trace                 = $e->getTrace();
            $error['trace_last_5'] = array_slice($trace, 0, 5);

//            dd($trace);

            return response()->json($error, 500);
        }

        $apiResponse = new ApiResponse($e->getMessage(), 500, "error");

        switch (true) {
            case $e instanceof BaseException: {
                $apiResponse->setStatusCode($e->getHttpStatus());
                $apiResponse->setMessage($e->getMessage());

                return $apiResponse->toResponse();
            }

            case $e instanceof ModelNotFoundException: {
                $apiResponse->setStatusCode(404);
                $apiResponse->setMessage('Requested information not found');

                return $apiResponse->toResponse();
            }

            default: {
                $apiResponse->setMessage('Something went wrong');

                return $apiResponse->toResponse();
            }
        }
    }
}
