<?php

namespace App\Repository;

use App\Models\Account;
use App\Models\Transaction;
use Brick\Math\BigInteger;

class TransactionRepository
{
    function store(int $source,int $destination, int $nominal,): Transaction
    {
        return Transaction::create([
            "source_account_id" => $source,
            "nominal" => $nominal,
            "destination_account_id" => $destination
        ]);
    }
}
