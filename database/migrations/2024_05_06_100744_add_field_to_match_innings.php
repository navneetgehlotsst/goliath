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
        Schema::table('match_innings', function (Blueprint $table) {
            //
            $table->string('current_score')->nullable();
            $table->string('current_wicket')->nullable();
            $table->string('current_overs')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_innings', function (Blueprint $table) {
            //
            $table->dropColumn('current_score');
            $table->dropColumn('current_wicket');
            $table->dropColumn('current_overs');
        });
    }
};
