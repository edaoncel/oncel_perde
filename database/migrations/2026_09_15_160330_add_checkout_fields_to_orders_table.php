<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'address')) {
                $table->text('address')->nullable()->after('total_price');
            }
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('card')->after('address');
            }
            if (!Schema::hasColumn('orders', 'card_name')) {
                $table->string('card_name')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'card_number')) {
                $table->string('card_number')->nullable()->after('card_name');
            }
            if (!Schema::hasColumn('orders', 'expiry')) {
                $table->string('expiry')->nullable()->after('card_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('orders', 'address') ? 'address' : null,
                Schema::hasColumn('orders', 'payment_method') ? 'payment_method' : null,
                Schema::hasColumn('orders', 'card_name') ? 'card_name' : null,
                Schema::hasColumn('orders', 'card_number') ? 'card_number' : null,
                Schema::hasColumn('orders', 'expiry') ? 'expiry' : null,
            ]));
        });
    }
};