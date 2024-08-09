<?php

namespace App\Http\Controllers;

use App\Http\Requests\TopUpRequest;
use App\Models\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopUpController extends Controller
{
    public function store(TopUpRequest $request)
    {
        $data = $request->validated();

        $user = Auth::guard('api')->user();

        $balance_before = $user->balance;
        $balance_after = $user->balance + $data['amount'];

        $data['balance_before'] = $balance_before;
        $data['balance_after'] = $balance_after;

        $topup = $user->topUps()->create($data);

        $user->balance = $balance_after;
        $user->save();

        return response()->json([
            "status" => "SUCCESS",
            "message" => "Top up successfully",
            "result" => $topup
        ], 200);
    }
}
