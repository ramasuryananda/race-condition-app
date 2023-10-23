<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $conection = "pgsql";

    protected $fillable = [
        "source_account_id",
        "destination_account_id",
        "nominal",
    ];
}
