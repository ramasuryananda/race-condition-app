<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\TransactionLog;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use App\Repository\AccountRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Repository\TransactionRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TransferBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Account $source;

    protected Account $destination;
    protected int $nominal;
    protected int $processLog;

    /**
     * Create a new job instance.
     */
    public function __construct(Account $source, Account $destination, $nominal, int $processLog)
    {
        $this->source = $source;
        $this->destination = $destination;
        $this->nominal = $nominal;
        $this->processLog = $processLog;
    }

    /**
     * Execute the job.
     */
    public function handle(AccountRepository $accRepo, TransactionRepository $transactionRepo): void
    {
        try {
            DB::beginTransaction();
            $sourceEndBalance = $this->source->balance - $this->nominal;
            $destinationEndBalance = $this->destination->balance + $this->nominal;
            if($sourceEndBalance<0){
                throw InvalidBalanceException::class;
            }

            $transactionRepo->store(source:$this->source->id,destination:$this->destination->id,nominal:$this->nominal,processLogId:$this->processLog);

            $accRepo->updateBalance($this->source->id,$sourceEndBalance);
            $accRepo->updateBalance($this->destination->id,$destinationEndBalance);

            TransactionLog::where("id",$this->processLog)->update([
                "status" => 1
            ]);
            
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            TransactionLog::where("id",$this->processLog)->update([
                "status" => 2
            ]);
            throw $th;
        }
    }
}
