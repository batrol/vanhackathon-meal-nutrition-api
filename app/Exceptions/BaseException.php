<?php namespace App\Exceptions;

use Illuminate\Http\Response;

class BaseException extends \Exception
{
    protected $httpStatus;

    public function __construct($message, $httpStatus = Response::HTTP_BAD_REQUEST, $code = 0)
    {
        $this->httpStatus = $httpStatus;

        parent::__construct($message, $code);
    }

    public function getHttpStatus()
    {
        return $this->httpStatus;
    }
}
