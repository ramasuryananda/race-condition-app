<?php

namespace App\Repository;

use App\Models\Account;

class AccountRepository
{
    function getForUpdate($id){
        return Account::forUpdate()->findOrFail($id);
    }

    function get($id){
        return Account::findOrFail($id);
    }

    function updateBalance(Account $account,int $balance){
        $account->balance = $balance;
        return $account->save();
    }

    function store(string $name,int $balance = 0){
        Account::create([
            "name" => $name,
            "balance" => $balance ?? 0
        ]);
    }
}
