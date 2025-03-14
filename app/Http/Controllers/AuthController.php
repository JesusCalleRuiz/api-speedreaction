<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $messages = [];

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $messages['email_format'] = 'El correo electrónico no es válido.';
        }
        if (User::where('name', $request->name)->exists()) {
            $messages['name'] = 'El nombre de usuario ya está en uso.';
        }

        if (User::where('email', $request->email)->exists()) {
            $messages['email'] = 'El correo electrónico ya está en uso.';
        }

        if (strlen($request->password) < 8) {
            $messages['password'] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if (!empty($messages)) {
            return response()->json(['errors' => $messages], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
}

