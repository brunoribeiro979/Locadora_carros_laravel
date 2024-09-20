<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciais = $request->all(['email', 'password']);
        // autenticacao por email e senha
        $token = auth('api')->attempt($credenciais);
        if ($token) { // usuario autenticado com sucesso
            // retornar um json web token
            return response()->json(['token' => $token]);
        } else { // erro de usuario ou senha
            return response()->json(['erro' => 'Usuário ou senha inválido!'], 403);

            // 401 = unauthorized -> não autorizado
            // 403 = forbidden -> proibido (login invalido)
        };
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['msg' => 'Logou realizado com sucesso!']);
    }

    public function refresh()
    {
        // esta sinalizando erro no refresh mas esta funcionando normalmente
        $token = auth('api')->refresh(); //cliente encaminhe um jwt valido
        return response()->json(['token' => $token]);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }
}
