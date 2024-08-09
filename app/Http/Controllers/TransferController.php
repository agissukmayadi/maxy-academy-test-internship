<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function store(TransferRequest $request)
    {
        $data = $request->validated();

        $user = Auth::guard('api')->user();

        if ($user->balance < $data["amount"]) {
            return response()->json([
                "status" => "FAILED",
                "message" => "Balance is not enough",
            ], 400);
        }

        $data["balance_before"] = $user->balance;
        $data["balance_after"] = $user->balance - $data["amount"];
        $user->balance = $data["balance_after"];
        $user->save();

        $transfer = $user->transfers()->create($data);

        $target_user = $transfer->targetUser;
        $target_user->balance = $target_user->balance + $data["amount"];
        $target_user->save();

        $result = [
            'transfer_id' => $transfer->transfer_id,
            'user_id' => $transfer->user_id,
            'amount' => $transfer->amount,
            'balance_before' => $transfer->balance_before,
            'balance_after' => $transfer->balance_after,
            'created_at' => $transfer->created_at,
        ];

        return response()->json([
            "status" => "SUCCESS",
            "message" => "Transfer successfully",
            "result" => $result
        ], 200);
    }

}