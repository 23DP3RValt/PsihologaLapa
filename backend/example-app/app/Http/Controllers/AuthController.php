<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
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
            'message' => 'User saved!'
        ]);
    }
}
