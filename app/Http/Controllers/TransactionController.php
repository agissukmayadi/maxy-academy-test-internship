<?php

namespace App\Http\Controllers;

use App\Models\TopUp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::guard('api')->user();
        $topUps = $user->topUps()->get()->map(function ($item) {
            return [
                'top_up_id' => $item->top_up_id,
                'status' => 'SUCCESS',
                'user_id' => $item->user_id,
                'transaction_type' => "CREDIT",
                'amount' => $item->amount,
                'remarks' => null,
                'balance_before' => $item->balance_before,
                'balance_after' => $item->balance_after,
                'created_at' => $item->created_at
            ];
        });

        $payments = $user->payments()->get()->map(function ($item) {
            return [
                'payment_id' => $item->payment_id,
                'status' => 'SUCCESS',
                'user_id' => $item->user_id,
                'transaction_type' => "DEBIT",
                'amount' => $item->amount,
                'remarks' => $item->remarks,
                'balance_before' => $item->balance_before,
                'balance_after' => $item->balance_after,
                'created_at' => $item->created_at
            ];
        });

        $transers = $user->transfers()->get()->map(function ($item) {
            return [
                'transfer_id' => $item->transfer_id,
                'status' => 'SUCCESS',
                'user_id' => $item->user_id,
                'target_user_id' => $item->target_user_id,
                'transaction_type' => "DEBIT",
                'amount' => $item->amount,
                'remarks' => $item->remarks,
                'balance_before' => $item->balance_before,
                'balance_after' => $item->balance_after,
                'created_at' => $item->created_at
            ];
        });

        $transactions = $topUps->merge($payments)->merge($transers)->sortByDesc('created_at')->values();

        return response()->json([
            "status" => "SUCCESS",
            "result" => $transactions
        ]);
    }
}