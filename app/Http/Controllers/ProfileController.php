<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        $user = Auth::guard('api')->user();
        $user->update($data);
        return response()->json([
            "status" => "SUCCESS",
            "message" => "Update profile successfully",
            "result" => $user
        ], 200);
    }
}