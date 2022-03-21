<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Exceptions\InvalidRequestException;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!JWTAuth::getToken())
                throw new InvalidRequestException('缺少token！', Response::HTTP_UNAUTHORIZED);

            if(!JWTAuth::parseToken()->check() || !JWTAuth::parseToken()->authenticate())
                throw new InvalidRequestException('登录状态失效，请重新登录！', Response::HTTP_UNAUTHORIZED);

            // 全局保存用户信息
            $request->jwt_user = auth('api')->user();

        } catch (JWTException $e) {
            throw new InvalidRequestException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $next($request);
    }
}
