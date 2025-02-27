<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user_id = Auth::user()->id ;
         $user = User::find($user_id);

        if (Auth::check() && $user->hasRole('admin')){
            return $next($request);
        }else{
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }
}
