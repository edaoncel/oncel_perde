<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@oncelperde.com'],
            [
                'name' => 'Öncel Yönetim',
                'password' => Hash::make('12345678'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('admins')->updateOrInsert(
            ['email' => 'edaoncel15@gmail.com'],
            [
                'name' => 'Developer',
                'password' => Hash::make('123456'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}