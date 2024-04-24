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
        Schema::create('add_question_to_matches', function (Blueprint $table) {
            $table->id();
            $table->integer('matchid')->default('0');
            $table->string('questionid')->default('0');
            $table->integer('over')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_question_to_matches');
    }
};
