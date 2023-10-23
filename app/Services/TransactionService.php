<?php

namespace App\Services;

use App\Exceptions\InvalidBalanceException;
use App\Jobs\TransferBalanceJob;
use Brick\Math\BigInteger;
use App\Models\Transaction;
use App\Models\TransactionLog;
use App\Repository\AccountRepository;
use App\Repository\TransactionRepository;
use Illuminate\Support\Facades\Cache;
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

    function storeAtomicLocking(int $source,int $destination, int $nominal):Transaction
    {
        try {
            DB::beginTransaction();
            
            $sourceAcc = $this->accRepo->get($source);
            $destinationAcc = $this->accRepo->get($destination);

            $transaction = Cache::lock(config("const.updateAccountBalanceLock"))->block(2,function () use($sourceAcc,$destinationAcc,$nominal){

                $sourceEndBalance = $sourceAcc->balance - $nominal;
                $destinationEndBalance = $destinationAcc->balance + $nominal;
                if($sourceEndBalance<0){
                    throw InvalidBalanceException::class;
                }
                $transaction = $this->transactionRepo->store(source:$sourceAcc->id,destination:$destinationAcc->id,nominal:$nominal);

                $this->accRepo->updateBalance($sourceAcc->id,$sourceEndBalance);
                $this->accRepo->updateBalance($destinationAcc->id,$destinationEndBalance);

                sleep(10);

                DB::commit();
                return $transaction;
            });

            return $transaction;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    function storeQueueSolution(int $source,int $destination, int $nominal): int
    {
        try {
            $sourceAcc = $this->accRepo->get($source);
            $destinationAcc = $this->accRepo->get($destination);

            $processLog = TransactionLog::create([
                "status" => 0
            ]);
            TransferBalanceJob::dispatch($sourceAcc,$destinationAcc,$nominal,$processLog->id);
            sleep(5);
            return $processLog->id;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
