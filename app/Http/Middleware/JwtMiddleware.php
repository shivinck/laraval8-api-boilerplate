<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Helpers\Response;

class JwtMiddleware extends BaseMiddleware
{


	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		try {
		   $user = JWTAuth::parseToken()->authenticate();
 		} catch (Exception $e) {
             if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return Response::sendError([], 'Token is Invalid', 403);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return Response::sendError([], 'Token is Expired', 401);
		    } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                return Response::sendError([], 'Token is Blacklisted', 400);
		    } else {
                return Response::sendError([], 'Authorization Token not found', 404);
		  }
		}
        return $next($request);
	}
}
