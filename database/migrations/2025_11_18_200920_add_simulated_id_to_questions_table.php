<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('simulated_id')->nullable()->after('board_id')->constrained('simulateds')->nullOnDelete();
            $table->unsignedSmallInteger('simulated_question_position')->default(0)->after('simulated_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('simulated_id');
        Schema::dropIfExists('simulated_question_position');
    }
};
