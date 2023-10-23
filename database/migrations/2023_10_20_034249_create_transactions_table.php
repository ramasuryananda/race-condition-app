<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("source_account_id");
            $table->unsignedBigInteger("destination_account_id");
            $table->decimal("nominal",20,2);
            $table->timestamps();

            $table->foreign("source_account_id")->on("accounts")->references("id");
            $table->foreign("destination_account_id")->on("accounts")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
