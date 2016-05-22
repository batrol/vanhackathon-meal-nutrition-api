<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function success($statusCode, $message = null, $data = null){
        return response()->json(['status' => 'success', 'message' => $message, 'data' => $data], $statusCode);
    }

    protected function error($statusCode, $message = null, $data = null){
        return response()->json(['status' => 'error', 'message' => $message, 'error' => $data], $statusCode);
    }
}
