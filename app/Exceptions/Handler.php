<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        // 自定义异常记录日志
        $this->reportable(function (InvalidRequestException $e) {
            Log::channel('api_error')->error($e->getMessage(), [
                    'errcode'           => $e->getCode(),
                    'request_params'    => request()->all(),
                    'path'              => request()->path(),
                    'method'            => request()->method(),
                    'ip'                => request()->getClientIp()
                ]
            );
        })->stop();

        $this->renderable(function  (NotFoundHttpException $e,  $request)  {
            if  ($request->is('api/*'))  {
                throw new InvalidRequestException($this->getErrorMessage('not_found'), $e->getStatusCode());
            }
        });

        $this->renderable(function  (MethodNotAllowedHttpException $e,  $request)  {
            if  ($request->is('api/*'))  {
                throw new InvalidRequestException($this->getErrorMessage('method_not_allowed'), $e->getStatusCode());
            }
        });
    }

    // 输出提示文字
    private function getErrorMessage($key)
    {
        $message = [
            'method_not_allowed'    => '请求的方式错误.',
            'not_found'             => '请求的方法不存在.',
        ];

        return $message[$key];
    }
}
