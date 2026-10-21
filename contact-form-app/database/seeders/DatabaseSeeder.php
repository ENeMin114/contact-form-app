<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // カテゴリとタグ、両方のシーダーを順番に呼び出します
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
        ]);
    }
}
