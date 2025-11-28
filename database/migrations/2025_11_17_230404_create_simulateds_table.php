<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('simulateds', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('image')->nullable();
            $table->string('title');
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->decimal('value', 8, 2)->default(0);
            $table->date('date_start');
            $table->date('date_end');
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('simulateds');
    }
};
