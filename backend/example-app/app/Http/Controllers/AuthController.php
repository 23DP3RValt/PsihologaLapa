<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Psychologist;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'birthdate' => 'required|date',
            'talrunis' => 'required|digits:8'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'birthdate' => $validated['birthdate'],
            'talrunis' => $validated['talrunis']
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User saved!',
            'role' => 'client',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function registerPsychologist(Request $request)
    {
        if (Psychologist::query()->exists()) {
            return response()->json([
                'message' => 'Only one psychologist account is allowed.'
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:psychologists,email',
            'password' => 'required|min:8|confirmed',
            'specialization' => 'required',
            'bio' => 'nullable|string',
        ]);

        $psychologist = Psychologist::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'specialization' => $validated['specialization'],
            'bio' => $validated['bio'] ?? null,
        ]);

        $token = $psychologist->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Psychologist saved!',
            'role' => 'psychologist',
            'user' => $psychologist,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();
        if ($user && Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Login successful',
                'role' => 'client',
                'user' => $user,
                'token' => $user->createToken('api-token')->plainTextToken
            ]);
        }

        $psychologist = Psychologist::where('email', $validated['email'])->first();
        if ($psychologist && Hash::check($validated['password'], $psychologist->password)) {
            return response()->json([
                'message' => 'Login successful',
                'role' => 'psychologist',
                'user' => $psychologist,
                'token' => $psychologist->createToken('api-token')->plainTextToken
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }
}
