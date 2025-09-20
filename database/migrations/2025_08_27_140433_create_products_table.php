<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('image')->nullable();
            $table->text('name');
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->boolean('status')->default(false);
            $table->enum('type', ['plan', 'midia', 'community']);
            $table->enum('time', ['free', 'monthly', 'semi-annual', 'yearly', 'lifetime']);

            $table->json('messages')->nullable();
            $table->json('files')->nullable();
            $table->json('posts')->nullable();

            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('product_plans');
    }
};
