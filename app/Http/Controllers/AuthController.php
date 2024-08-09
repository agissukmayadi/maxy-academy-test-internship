<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Token;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            "first_name" => $data["first_name"],
            "last_name" => $data["last_name"],
            "phone_number" => $data["phone_number"],
            "address" => $data["address"],
            "pin" => Hash::make($data['pin']),
            "balance" => 0
        ]);

        return response()->json([
            "status" => "SUCCESS",
            "message" => "User created successfully",
            "result" => $user
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where("phone_number", $data["phone_number"])->first();
        if (!$user || !Hash::check($data["pin"], $user->pin)) {
            return response()->json([
                "status" => "FAILED",
                "message" => "Phone number and pin does not match",
            ], 401);
        }

        $access_token = auth('api')->login($user);

        $refreshToken = RefreshToken::create([
            'user_id' => $user->user_id,
            'token' => Str::uuid(),
        ]);

        return response()->json([
            "status" => "SUCCESS",
            "message" => "Login successfully",
            'result' => [
                'access_token' => $access_token,
                'refresh_token' => $refreshToken->token
            ]
        ], 200);
    }

    public function logout()
    {
        $user = auth('api')->user();
        RefreshToken::where('user_id', $user->user_id)->delete();
        auth('api')->logout();
        return response()->json([
            "status" => "SUCCESS",
            "message" => "Logout successfully",
        ], 200);
    }
}
