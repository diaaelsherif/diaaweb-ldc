<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
//use Illuminate\Support\Facades\Auth;

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

            event(new Registered($user));

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

        $user = User::where('email',$request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
        return response()->json([
            'message' => 'Incorrect credentials'
        ], 401);
        }

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

        // Return data
        return response()->json([
            'message' => 'User successfully logged in',
            'token_type' => 'Bearer',
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();
        if ($user)
        {
            $user->tokens()->delete();

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

    // Email Verification Handler
    public function verifyEmailHandler(EmailVerificationRequest $request)
    {
        $user = User::find($request->route('id'));

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException;
        }

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;

        if ($user->markEmailAsVerified())
        {
            event(new Verified($user));

            return response()->json([
                'message' => 'User email verified',
            ], 200);
        }
        else
        {
            return response()->json([
                'message' => 'User email not verified',
            ], 500);
        }
    }

    // Resending the Verification Email Handler
    public function verifyEmailResend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification link sent',
        ], 200);
    }
}
