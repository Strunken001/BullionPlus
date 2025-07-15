<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function getPaginatedHistory(Request $request)
    {
        $limit = $request->limit ?? 20;
        $transactions = Transaction::where('type', $request->type)->where('user_id', auth()->user()->id)->latest()->paginate($limit);

        return response()->json([
            "status" => "success",
            "message" => "purchase history returned",
            "data" => $transactions
        ]);
    }
}
