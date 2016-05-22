<?php

namespace App\Http\Controllers;

use GoCanada\Popos\ApiResponse;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($statusCode, $message = null, $data = null){
        $apiResponse = new ApiResponse($message, $statusCode, 'success');
        $apiResponse->setData($data);

        return $apiResponse->toResponse();
    }

    protected function error($statusCode, $message = null, $data = null){
        $apiResponse = new ApiResponse($message, $statusCode, 'error');
        $apiResponse->setData($data);

        return $apiResponse->toResponse();
    }
}
