<?php

namespace App\Http\Middleware;

use Closure;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Check if the user has the admin attribute
            if (Auth::user()->isadmin) {
                return $next($request);
            }
        }


         return response()->json(["error"=>'You do not have permission to access this page.'],Response::HTTP_NOT_FOUND);




    }
}
