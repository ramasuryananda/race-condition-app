<?php

namespace App\Services;

use App\Exceptions\InvalidBalanceException;
use Brick\Math\BigInteger;
use App\Models\Transaction;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected AccountRepository $accRepo;
    protected TransactionRepository $transactionRepo;
    public function __construct(AccountRepository $accRepo, TransactionRepository $transactionRepo) {
        $this->accRepo = $accRepo;
        $this->transactionRepo = $transactionRepo;
    }

    function store(int $source,int $destination, int $nominal):Transaction
    {
        try {
            DB::beginTransaction();
            DB::statement("SET innodb_lock_wait_timeout = 1");//set timeout
            $transactionAccount = $this->accRepo->getForUpdateMultipleAccount([$source,$destination]);
            $sourceAcc = $transactionAccount[$source];
            $destinationAcc = $transactionAccount[$destination];

            $sourceEndBalance = $sourceAcc->balance - $nominal;
            $destinationEndBalance = $destinationAcc->balance + $nominal;
            if($sourceEndBalance<0){
                throw InvalidBalanceException::class;
            }
            sleep(10);

            $transaction = $this->transactionRepo->store(source:$source,destination:$destination,nominal:$nominal);

            $this->accRepo->updateBalance($source,$sourceEndBalance);
            $this->accRepo->updateBalance($destination,$destinationEndBalance);
            DB::commit();
            return $transaction;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
