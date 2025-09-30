<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('notebook_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('notebook_id')->nullable()->constrained('notebooks')->nullOnDelete(); // não excluir o registro
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->tinyInteger('question_position')->default(1);
            $table->foreignId('answer_id')->nullable()->constrained('question_alternatives')->nullOnDelete();
            $table->tinyInteger('answer_result')->default(0); // 0 = sem resposta, 1 = correta, 2 = incorreta
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void {
        Schema::dropIfExists('notebook_questions');
    }
};
