<?php

namespace App\Http\Middleware;

use App\Models\MobileApiToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureMobileApiTokenIsValid
{
    public function handle(Request $request, Closure $next)
    {
        $authorization = (string) $request->header('Authorization', '');
        $token = '';

        if (str_starts_with($authorization, 'Bearer ')) {
            $token = trim(substr($authorization, 7));
        }

        if ($token === '') {
            return response()->json([
                'message' => 'Missing Authorization Bearer token.',
            ], 401);
        }

        $mobileApiToken = MobileApiToken::findValid($token);

        if ($mobileApiToken === null || $mobileApiToken->user === null) {
            return response()->json([
                'message' => 'Invalid or expired mobile API token.',
            ], 401);
        }

        $mobileApiToken->markAsUsed();

        $request->setUserResolver(fn () => $mobileApiToken->user);
        Auth::setUser($mobileApiToken->user);
        $request->attributes->set('mobile_api_token', $mobileApiToken);

        return $next($request);
    }
}
