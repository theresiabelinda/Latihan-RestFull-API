<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $autenticated = true;

        #jika token null
        if(!$token){
            $autenticated = false;
        }

        #jika ada token, cek apakah ada di DB
        $user = User::where('token', $token)->first();
        if(!$user){
            $autenticated = false;
        }else{
            #dilogin-kan langsung
            Auth::login($user);
        }

        if($autenticated){
            return $next($request);
        }else{
            #pesan kesalahan (Unauthorized)
            return response()->json([
                "error" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
