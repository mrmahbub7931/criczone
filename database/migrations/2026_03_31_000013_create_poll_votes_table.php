<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('poll_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('option_index');
            $table->string('voter_id', 64); // sha256 of IP+UA
            $table->timestamps();
            $table->unique(['poll_id', 'voter_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('poll_votes'); }
};
