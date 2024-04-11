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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->enum('question_type',['dot_ball','boundary','wicket','run'])->default('dot_ball');
            $table->enum('status',['active','inactive'])->default('active');
            $table->enum('conditions',['greater_than','less_than','equal','greater_than_equal','less_than_equal','not_equal'])->default('equal');
            $table->integer('quantity')->default('0');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
