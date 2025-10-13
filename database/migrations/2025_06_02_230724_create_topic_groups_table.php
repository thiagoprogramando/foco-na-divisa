<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   
    public function up(): void {
        Schema::create('topic_groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('content_id')->constrained('contents')->onDelete('cascade');
            $table->string('title')->unique();
            $table->json('topics')->nullable();
            $table->integer('order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('topic_groups');
    }
};
