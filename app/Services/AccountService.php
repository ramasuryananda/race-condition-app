<?php

namespace App\Services;

use App\Models\Account;
use App\Repository\AccountRepository;

class AccountService
{
    protected AccountRepository $repo;
    public function __construct(AccountRepository $accRepo) {
        $this->repo = $accRepo;
    }
    
    function createAccount(String $name, int $balance = 0): Account
    {
        return $this->repo->store($name,$balance);
    }
}
