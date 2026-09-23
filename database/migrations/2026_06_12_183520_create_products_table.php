<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_group_id')->nullable()->constrained('product_groups')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('shipping_price', 10, 2)->default(0.00);
            $table->integer('stock')->default(0);

            $table->string('color')->nullable();
            $table->string('colorRGB')->nullable();
            $table->boolean('is_main')->default(false);
            $table->string('image')->nullable();
            
            $table->timestamps();
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};