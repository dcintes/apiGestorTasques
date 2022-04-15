<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;



class UserAuthController extends Controller
{
    /**
     * Registre de l'usuari, retorna token autenticació
     */
    public function register(Request $request)
    {
        $data = $this->validate($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:50',
            'password' => 'required|confirmed|min:8|max:16',
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken('API Token')->accessToken;

        return response()->json(['user_id' => $user->id, 'token' => $token], 201);
    }

    /**
     * Autenticació d'un usuari, retorna token autenticació
     */
    public function login(Request $request)
    {
        $data = $this->validate($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response()->json(['error_message' => 'Incorrect Details. 
            Please try again'], 401);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return response()->json(['user_id' => auth()->user()->id, 'token' => $token], 200);
    }
}
