<?php namespace GoCanada\Popos;

class ApiResponse
{
    private $message;
    private $statusCode;
    private $status;
    private $data;

    public function __construct($message, $statusCode, $status)
    {
        $this->message    = $message;
        $this->statusCode = $statusCode;
        $this->status = $status;
    }

    public function toArray()
    {
        return [
            'status_code' => $this->statusCode,
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }

    public function toResponse()
    {
        return response()->json([
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ], $this->statusCode);
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

}