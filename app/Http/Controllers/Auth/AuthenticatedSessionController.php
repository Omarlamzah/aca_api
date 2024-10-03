<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
         $request->user()->tokens()->delete();
            $token = $request->user()->createToken($request->user()->email);


        return response()->json([
            'user' => $request->user(),
            'token' => $token->plainTextToken,
            'message' => 'Login successful'
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();



      //  return response()->json(['message' => 'Logout successful'])->header('Content-Type', 'application/json');

        return response()->noContent();



    }
}
