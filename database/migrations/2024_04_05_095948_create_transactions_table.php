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
            $table->id();
            $table->integer('user_id')->default('0');
            $table->string('amount')->nullable();
            $table->string('transaction_id')->nullable();
            $table->enum('transaction_type',['admin-payout','add-wallet','pay','winning-amount','withdrawal-amount'])->default('winning-amount');
            $table->enum('payment_mode',['credit','debite'])->default('debite');
            $table->timestamps();
            $table->softDeletes();
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
