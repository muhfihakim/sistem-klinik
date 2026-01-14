<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt login menggunakan guard 'api'
        if (! $token = auth('api')->attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Unauthorized: Email atau Password Salah'], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil (JWT Generated)',
            'token'   => $token,
            'user'    => auth('api')->user()
        ]);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Token telah dihapus (Logout Berhasil)']);
    }
}
