<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponse;

class IsCheif

{
    use ApiResponse;
    /**
     * Handle an incoming request.
     
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      $user=Auth::user();
        if (!$user) {
          return $this->errorResponse('Unauthenticated', 401);
        }
      if ($user->role !== 'cheif') {
          return $this->errorResponse('This Action not allowed for this user', 403);
        }

        return $next($request);
    }
}
