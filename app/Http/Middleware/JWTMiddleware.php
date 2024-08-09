<?php

namespace App\Http\Middleware;

use App\Models\RefreshToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class JWTMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            if ($request->hasCookie('refresh_token')) {
                $refreshToken = RefreshToken::where('token', $request->cookie('refresh_token'))->first();
                if ($refreshToken) {
                    $access_token = auth('api')->login($refreshToken->user);

                    $response = $next($request);

                    if ($request->route()->getName() == 'logout') {
                        return $response;
                    }

                    if ($response instanceof Response && $response->headers->get('Content-Type') === 'application/json') {
                        $content = json_decode($response->getContent(), true);
                        $content['new_access_token'] = $access_token;
                        $response->setContent(json_encode($content));
                    } else {
                        $response->headers->set('Authorization', 'Bearer ' . $access_token);
                    }
                    return $response;
                } else {
                    return response()->json(['message' => 'Unauthenticated'], 401);
                }
            } else {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        }
        return $next($request);
    }

}