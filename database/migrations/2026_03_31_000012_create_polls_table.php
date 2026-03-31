<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->json('options');          // array of option strings
            $table->boolean('is_active')->default(false);
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('polls'); }
};
