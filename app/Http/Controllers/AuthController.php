<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // REGISTRO DE USUÁRIO
    public function register(Request $request)
    {
        // 1. Validar os dados que chegaram do Frontend
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email', // email único na tabela users
            'password' => 'required|string|confirmed' // exige um campo password_confirmation
        ]);

        // 2. Criar o usuário no banco
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']) // Criptografa a senha
        ]);

        // 3. Criar o Token (para ele já sair logado)
        $token = $user->createToken('myapptoken')->plainTextToken;

        // 4. Retornar resposta
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // 1. Checar se o email existe
        $user = User::where('email', $fields['email'])->first();

        // 2. Checar senha (se user não existe OU a senha não bater)
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        // 3. Criar Token
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        // Apaga os tokens do usuário atual
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Deslogado com sucesso'
        ]);
    }
}