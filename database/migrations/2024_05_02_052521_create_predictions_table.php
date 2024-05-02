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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->default('0');
            $table->integer('match_id')->default('0');
            $table->integer('question_id')->default('0');
            $table->integer('over_id')->default('0');
            $table->enum('answere',['0','1','2'])->comment('0: Answere Not given, 1: Yes , 2: No')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
