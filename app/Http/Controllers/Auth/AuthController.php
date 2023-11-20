<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => ['Incorrect credentials'],
            ]);
        }

        $user->tokens()->delete();

        return new LoginResource($user);
    }

    public function getUser(Request $request)
    {
        return new UserResource($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }
}
