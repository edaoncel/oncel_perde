<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_group_id']);
            
            $table->dropColumn('product_group_id');
            
            $table->foreignId('product_group_id')->nullable()->constrained('product_groups')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['product_group_id']);
            $table->dropColumn('product_group_id');
        });
    }
};