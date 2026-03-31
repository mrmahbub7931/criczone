<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_scores', function (Blueprint $table) {
            $table->id();
            $table->string('team_a', 60);
            $table->string('team_b', 60);
            $table->string('score_a', 40)->default('—');
            $table->string('score_b', 40)->default('—');
            $table->string('overs', 20)->nullable();
            $table->string('status', 100)->default('Upcoming');
            $table->string('match_type', 60)->default('T20'); // Test | ODI | T20 | T20I
            $table->string('venue', 150)->nullable();
            $table->timestamp('match_time')->nullable();
            $table->boolean('is_live')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_scores');
    }
};
