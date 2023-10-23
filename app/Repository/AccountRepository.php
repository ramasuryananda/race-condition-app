<?php

namespace App\Repository;

use App\Models\Account;

class AccountRepository
{
    function getForUpdate($id){
        return Account::lockForUpdate()->findOrFail($id);
    }
    
    function getForUpdateMultipleAccount(array $id){
        return Account::lockForUpdate()->whereIn("id",$id)->get()->keyBy("id");
    }

    function get($id){
        return Account::findOrFail($id);
    }

    function updateBalance(int $accountId,int $balance){
        return Account::where("id",$accountId)->update(["balance"=>$balance]);
    }

    function store(string $name,int $balance = 0): Account
    {
        return Account::create([
            "name" => $name,
            "balance" => $balance ?? 0
        ]);
    }
}
