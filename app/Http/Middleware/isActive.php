<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class isActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Check if the user is authenticated
         if (Auth::check()) {
            // Check if the user's email is verified
            if (Auth::user()->isactive) {
                return $next($request);
            }
        }

        // Redirect or respond as needed for non-verified users
       // return redirect('/notfound')->with('error', 'Your email is not activated.');
       // return Redirect::to('/login')->withErrors(['error' => 'Your email is not activated.']);
        return response()->json(["error"=>'Your account is not yet activated. You will be notified once it is activated.'],Response::HTTP_NOT_FOUND);



    }

}
