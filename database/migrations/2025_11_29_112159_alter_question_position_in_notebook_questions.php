<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('notebook_questions', function (Blueprint $table) {
            $table->unsignedSmallInteger('question_position')->default(1)->change();
        });
    }

    public function down(): void {
        Schema::table('notebook_questions', function (Blueprint $table) {
            $table->tinyInteger('question_position')->default(1)->change();
        });
    }
};
