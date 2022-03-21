<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsXss
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        // 忽略xss的域名
        $ignore_xss = ['laravel-admin.com'];

        // 防止用户xss提交
        if(!in_array($request->server('HTTP_HOST'), $ignore_xss)){
            array_walk_recursive($input,function(&$input) {
                $input = strip_tags($input);
            });

            $request->merge($input);
        }

        return $next($request);
    }
}
