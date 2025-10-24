<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\User;

class AuthController extends Controller
{
    public function authenticateLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:255',
                'password' => 'required|string|max:64'
            ]);

            $user = User::where('email', $request->email)->first();
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            Log::error('Error while loading login view. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function logoutUser(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                $user->currentAccessToken()->delete();
            }

            return response()->json([
                'message' => 'Logout successful',
            ]);
        } catch (Exception $e) {
            Log::error('Error while trying log out user: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
