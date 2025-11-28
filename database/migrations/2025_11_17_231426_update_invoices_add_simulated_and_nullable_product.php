<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    
    public function up(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->foreignId('simulated_id')->nullable()->constrained('simulateds')->nullOnDelete()->after('product_id');
        });
    }

    public function down(): void {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['simulated_id']);
            $table->dropColumn('simulated_id');
            $table->foreignId('product_id')->nullable(false)->change();
        });
    }
};
