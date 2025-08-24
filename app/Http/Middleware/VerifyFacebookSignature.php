<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyFacebookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-Hub-Signature-256');
        if (!$signature) {
            return response('Missing signature', 400);
        }

        [$algo, $hash] = explode('=', $signature, 2);
        $expected = hash_hmac('sha256', $request->getContent(), env('FB_APP_SECRET'));
        if (!hash_equals($expected, $hash)) {
            return response('Invalid signature', 403);
        }

        return $next($request);
    }
}

