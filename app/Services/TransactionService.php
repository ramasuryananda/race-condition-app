<?php

namespace App\Repository;

use App\Exceptions\InvalidBalanceException;
use Brick\Math\BigInteger;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;

class TransactionService
{
    protected AccountRepository $accRepo;
    protected TransactionRepository $transactionRepo;
    public function __construct(AccountRepository $accRepo, TransactionRepository $transactionRepo) {
        $this->accRepo = $accRepo;
        $this->transactionRepo = $transactionRepo;
    }

    function store(BigInteger $accountId, int $nominal, bool $mutation):Transaction
    {
        try {
            DB::beginTransaction();

            $account = $this->accRepo->getForUpdate($accountId);
            $endBalance = $mutation ? $account->balance + $nominal : $account->balance - $nominal;
            if($endBalance<0){
                throw InvalidBalanceException::class;
            }

            $transaction = $this->transactionRepo->store($account->id,$nominal,$mutation);

            $this->accRepo->updateBalance($account,$endBalance);

            return $transaction;

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
