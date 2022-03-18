<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 接口成功返回
    protected function success($data = [], $message = 'success')
    {
        // 记录成功日志
        Log::channel('api_success')->info('调用成功', [
            'path'   => request()->path(),
            'method' => request()->method(),
            'request_params' => request()->all(),
            'response_data'  => $data,
            'authorization'  => request()->header('Authorization', '') ?? '',
            'request_ip'     => request()->getClientIp()
        ]);

        // 返回信息
        return response()->json([
            'errcode' => 0,
            'message' => $message,
            'data'    => $data
        ]);
    }
}
