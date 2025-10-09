<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('question_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_answer_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('comment_id')->nullable()->constrained('question_comments')->onDelete('cascade');
            $table->longText('comment');
            $table->longText('comment_answer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_comments');
    }
};
