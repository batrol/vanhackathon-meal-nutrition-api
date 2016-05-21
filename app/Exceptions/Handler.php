<?php

namespace App\Exceptions;

use Exception;
use GoCanada\Popos\ApiError;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

            return response()->json($error, 500);
        }

        $apiError = new ApiError($e->getMessage(), 500);

        switch (true) {
            case $e instanceof BaseException: {
                $apiError->setStatusCode($e->getHttpStatus());
                $apiError->setMessage($e->getMessage());
                return response()->json($apiError->toArray(), $apiError->getStatusCode());
            }

            case $e instanceof ModelNotFoundException: {
                $apiError->setMessage('Requested information not found');
                $apiError->setStatusCode(404);

                return response()->json($apiError->toArray(), $apiError->getStatusCode());
            }

            default: {
                $apiError->setMessage('Something went wrong');
                $apiError->setStatusCode(500);

                return response()->json($apiError->toArray(), $apiError->getStatusCode());
            }
        }

        return parent::render($request, $e);
    }
}
