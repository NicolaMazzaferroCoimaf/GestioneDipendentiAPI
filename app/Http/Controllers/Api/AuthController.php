<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\LogsUserAction;

class AuthController extends Controller
{
    use LogsUserAction;

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $this->logUserAction('auth', 'Nuova registrazione', [
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
        ]);

        return response()->json([
            'message' => 'Registrazione completata',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            $this->logUserAction('auth', 'Tentativo di login fallito', [
                'username' => $request->username,
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Credenziali non valide'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->logUserAction('auth', 'Login effettuato', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $this->logUserAction('auth', 'Logout effettuato', [
            'user_id' => $user->id,
            'username' => $user->username,
            'ip' => $request->ip(),
        ]);

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout eseguito con successo']);
    }
}