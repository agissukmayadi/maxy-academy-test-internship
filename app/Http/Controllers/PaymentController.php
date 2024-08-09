<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function store(PaymentRequest $request)
    {
        $data = $request->validated();

        $user = Auth::guard('api')->user();

        if ($user->balance < $data['amount']) {
            return response()->json([
                "status" => "FAILED",
                "message" => "Balance is not enough",
            ], 400);
        }

        $data['balance_before'] = $user->balance;
        $data['balance_after'] = $user->balance - $data['amount'];

        $payment = $user->payments()->create($data);

        $user->balance = $data['balance_after'];
        $user->save();

        return response()->json([
            "status" => "SUCCESS",
            "message" => "Payment successfully",
            "result" => $payment
        ], 200);
    }
}