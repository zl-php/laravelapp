<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;

class InvalidRequestException extends Exception
{
   public function __construct($message = "", $code = 400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        $result =  config('app.debug') ? [
            'errcode' => $this->code,
            'message' => $this->getMessage(),
            'data' => [],
            'exception' => get_class($this),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => collect($this->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ] : [
            'errcode' => $this->code,
            'message' => $this->getMessage(),
            'data' => []
        ];

        // 判断返回500错误
        if($this->code == Response::HTTP_INTERNAL_SERVER_ERROR)
            return response()->json($result, $this->code);

        return response()->json($result, Response::HTTP_OK);
    }
}
