<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->saveLoginAttempt($request, true);
            return response()->json([
                'message' => ['Incorrect credentials'],
            ], 401);
        }

        $user->tokens()->delete();

        $this->saveLoginAttempt($request);
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

    private function saveLoginAttempt(Request $request, $failed = false)
    {
        preg_match('/(\d+\.\d+\.)(\d+\.\d+)/', $request->ip(), $matches);

        DB::table('login_attempts')->insert([
            'date' => now(),
            'ip' => $matches[2],
            'user' => $request->name,
            'failed' => $failed,
        ]);
    }
}
