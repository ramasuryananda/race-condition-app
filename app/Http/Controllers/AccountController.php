<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\StoreRequest;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected AccountService $service;

    public function __construct(AccountService $service) {
        $this->service = $service;
    }

    function store(StoreRequest $request){
        $accountRequest = $request->validated();
        $account = $this->service->createAccount(
            name:$accountRequest["name"],
            balance:isset($accountRequest["balance"]) ? $accountRequest["balance"] : 0
        );
        return response()->json([
            "message" => "success creating account",
            "data"=> $account
        ]);
    }
}
