<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $conection = "pgsql";

    protected $fillable = [
        "name",
        "balance",
    ];

    public function transsactions(){
        return $this->hasMany(Transaction::class,"account_id","id");
    }
}
