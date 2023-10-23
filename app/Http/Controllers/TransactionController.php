<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transaction\StoreRequest;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{

    protected TransactionService $service;
    public function __construct(TransactionService $service) {
        $this->service = $service;
    }

    public function store(StoreRequest $request){
        $transactionRequest = $request->validated();
        $transaction = $this->service->storeQueueSolution(
            source:$transactionRequest["source_account_id"],
            destination:$transactionRequest["destination_account_id"],
            nominal:$transactionRequest["nominal"],
        );

        return response()->json([
            "message" => "success storing transaction",
            "data" => $transaction
        ],Response::HTTP_CREATED);
    }
}
