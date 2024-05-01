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
        Schema::create('competition_matches', function (Blueprint $table) {
            $table->id();
            $table->integer('competiton_id')->default('0');
            $table->integer('match_id')->default('0');
            $table->string('match');
            $table->string('teama_name');
            $table->string('teama_short_name');
            $table->string('teama_img');
            $table->string('teamb_name');
            $table->string('teamb_short_name');
            $table->string('teamb_img');
            $table->string('formate');
            $table->string('match_start_date');
            $table->string('match_start_time');
            $table->enum('status',['Completed','Scheduled','Live','Cancelled'])->default('Completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_matches_lists');
    }
};
