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
        Schema::table('competition_matches', function (Blueprint $table) {
            $table->string('note');
            $table->string('teamaid');
            $table->string('teambid');
            $table->string('teamascorefull');
            $table->string('teambscorefull');
            $table->string('teamascore');
            $table->string('teambscore');
            $table->string('teamaover');
            $table->string('teambover');
            $table->integer('live_innings')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competition_matches', function (Blueprint $table) {
            $table->dropColumn('note');
            $table->dropColumn('teamaid');
            $table->dropColumn('teambid');
            $table->dropColumn('teamascorefull');
            $table->dropColumn('teambscorefull');
            $table->dropColumn('teamascore');
            $table->dropColumn('teambscore');
            $table->dropColumn('teamaover');
            $table->dropColumn('teambover');
            $table->dropColumn('live_innings');
        });
    }
};
