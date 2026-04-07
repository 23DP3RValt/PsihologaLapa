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
            'personas_kods' => 'required|regex:/^[0-9]{6}-[0-9]{5}$/',
            'talrunis' => 'required|digits:8'
        ]);

        User::create([
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']), // 🔐
            'birthdate' => $validated['birthdate'],
            'personas_kods' => $validated['personas_kods'],
            'talrunis' => $validated['talrunis']
        ]);

        return response()->json([
            'message' => 'User saved!',
            'role' => 'client'
        ]);
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

        return response()->json([
            'message' => 'Psychologist saved!',
            'role' => 'psychologist',
            'user' => $psychologist,
        ]);
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
                'user' => $user
            ]);
        }

        $psychologist = Psychologist::where('email', $validated['email'])->first();
        if ($psychologist && Hash::check($validated['password'], $psychologist->password)) {
            return response()->json([
                'message' => 'Login successful',
                'role' => 'psychologist',
                'user' => $psychologist
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }
}
