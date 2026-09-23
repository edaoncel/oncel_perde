<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_messages', 'phone')) {
                $table->string('phone')->after('name'); 
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            if (Schema::hasColumn('contact_messages', 'phone')) {
                $table->dropColumn('phone');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
        });
    }
};