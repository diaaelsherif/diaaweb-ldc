<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
  public function register(Request $request)
  {
      $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
      ]);

      $user = User::create($validated);

      if ($user)
      {
        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

        Auth::login($user);

        // Return data
        return response()->json([
            'message' => 'User registered successfully',
            'token_type' => 'Bearer',
            'token' => $token
        ], 201);
      }
      else
      {
        return response()->json([
            'message' => 'User not registered successfully',
        ], 500);
      }
  }

  public function login(Request $request)
  {
      $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
      ]);

      if (Auth::attempt($validated)) {
        $request->session()->regenerate();

        $user = $request->user();

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

        // Return data
        return response()->json([
            'message' => 'User successfully logged in',
            'token_type' => 'Bearer',
            'token' => $token
        ], 200);
      }

      throw ValidationException::withMessages([
        'credentials' => 'Sorry, incorrect credentials',
      ]);
  }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user)
        {
            $user->tokens()->delete();
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'User logged out successfully',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }
}
