<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Classes\ApiResponseClass;
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

      Auth::login($user);

      return ApiResponseClass::sendResponse(Auth::user(), 'User successfully registered', 200);
  }

  public function login(Request $request)
  {
      $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:8',
      ]);

      if (Auth::attempt($validated)) {
        $request->session()->regenerate();

        // Sign in user
        Auth::login(Auth::user());

        // Return data
        return ApiResponseClass::sendResponse(Auth::user(), 'User successfully logged in', 200);
      }

      throw ValidationException::withMessages([
        'credentials' => 'Sorry, incorrect credentials',
      ]);
  }

  public function logout(Request $request)
  {
      Auth::logout();

      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return ApiResponseClass::sendResponse('User successfully logged out', '', 200);
  }
}
