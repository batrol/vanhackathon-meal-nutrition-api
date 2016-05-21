<?php namespace GoCanada\Popos;

class ApiError
{
    private $message;
    private $statusCode;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return ApiError
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return ApiError
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function __construct($message, $statusCode)
    {
        $this->message    = $message;
        $this->statusCode = $statusCode;
    }

    public function toArray()
    {
        return [
            'status'      => 'error',
            'status_code' => $this->statusCode,
            'message'     => $this->message,
        ];
    }

}