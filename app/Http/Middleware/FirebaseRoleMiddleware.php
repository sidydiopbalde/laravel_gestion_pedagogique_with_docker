<?php

namespace App\Http\Middleware;

use Closure;
use Kreait\Firebase\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Auth;

class FirebaseRoleMiddleware
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function handle($request, Closure $next)
    {
        if ($request->hasHeader('Authorization')) {
            try {
                $token = str_replace('Bearer ', '', $request->header('Authorization'));
                $verifiedIdToken = $this->firebaseAuth->verifyIdToken($token);
                $uid = $verifiedIdToken->claims()->get('sub');

                $user = \App\Models\User::where('firebase_uid', $uid)->first();
                if (!$user) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                // Authentifier l'utilisateur dans Laravel
                Auth::login($user);

            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid Firebase Token'], 401);
            }
        }

        return $next($request);
    }
}

