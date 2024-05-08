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
        Schema::create('overballes', function (Blueprint $table) {
            $table->id();
            $table->integer('match_id')->default('0');
            $table->integer('innings')->default('0');
            $table->integer('over_no')->default('0');
            $table->integer('ball_no')->default('0');
            $table->integer('score')->default('0');
            $table->integer('noball_dismissal')->default('0');
            $table->integer('run')->default('0');
            $table->integer('noball_run')->default('0');
            $table->integer('wide_run')->default('0');
            $table->integer('bye_run')->default('0');
            $table->integer('legbye_run')->default('0');
            $table->integer('bat_run')->default('0');
            $table->enum('noball',['0','1'])->comment('0: false, 1: true')->default('0');
            $table->enum('wideball',['0','1'])->comment('0: false, 1: true')->default('0');
            $table->enum('six',['0','1'])->comment('0: false, 1: true')->default('0');
            $table->enum('four',['0','1'])->comment('0: false, 1: true')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overballes');
    }
};
