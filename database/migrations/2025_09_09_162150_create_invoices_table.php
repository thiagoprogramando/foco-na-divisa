<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('value', 10, 2);
            $table->date('due_date');
            $table->tinyInteger('payment_status')->default(0)->comment('0=pending,1=paid,2=canceled');
            $table->json('payment_splits')->nullable();
            $table->longText('payment_token');
            $table->longText('payment_url');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('invoices');
    }
};
