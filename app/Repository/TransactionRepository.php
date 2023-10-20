<?php

namespace App\Repository;

use App\Models\Account;
use App\Models\Transaction;
use Brick\Math\BigInteger;

class TransactionRepository
{
    function store(BigInteger $accountId, int $nominal, bool $mutation): Transaction
    {
        return Transaction::create([
            "account_id" => $accountId,
            "nominal" => $nominal,
            "mutation" => $mutation
        ]);
    }
}
