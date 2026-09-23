<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Tül Perdeler',
            'Fon Perdeler',
            'Stor Perdeler',
            'Özel Tasarım',
            'Çeyiz & Pike Grubu',
            'Ev Tekstili',
            'Terzi & Perde Malzemeleri'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName)
            ]);
        }
    }
}