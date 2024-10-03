<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the user's profile information.
     */
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'updatedUser.name' => 'required|string|max:255',
            'updatedUser.email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,' . auth()->id(), // Check uniqueness excluding the current user
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = $request->user();

        if ($request->has('updatedUser.name')) {
            $user->name = $request->input('updatedUser.name');
        }

        if ($request->has('updatedUser.email')) {
            $user->email = $request->input('updatedUser.email');
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json(['status' => 'profile-updated'," user"=> $user]);
    }    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->delete();

        // Apply web middleware to have access to session methods
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}

