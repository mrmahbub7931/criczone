<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('article_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->string('language', 5);         // 'en' or 'bn'
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->enum('status', ['pending', 'processing', 'done', 'failed'])->default('pending');
            $table->string('provider')->nullable(); // 'claude' or 'openai'
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->unique(['article_id', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_translations');
    }
};
