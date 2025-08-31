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

      Auth::login($user);

      return;
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
        return $this->successfullRequest(Auth::user(), 'User successfully logged in', 200);
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

      return;
  }
}
