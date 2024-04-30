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
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->integer('competiton_id')->default('0');
            $table->string('title');
            $table->enum('type',['international','domestic','women','youth'])->default('international');
            $table->enum('competition_type',['mixed','odi','test','t20i','firstclass','lista','t20','youthodi','youthodi','youtht20','womenodi','woment20','t10'])->default('mixed');
            $table->string('date_start');
            $table->string('date_end');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competition_lists');
    }
};
