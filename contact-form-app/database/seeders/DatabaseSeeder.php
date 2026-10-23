<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. まず、昨日作成したカテゴリとタグの初期データを登録します
        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
        ]);

        // 現在登録されているすべてのタグを取得
        $tags = Tag::all();

        // 2. 🔴 お問い合わせデータを35件自動生成し、中間テーブルへタグもランダムに紐付けます
        Contact::factory(35)->create()->each(function ($contact) use ($tags) {
            // 各お問い合わせに対して、0〜3個のタグをランダムに選んで多対多の同期（保存）を行います
            $randomTags = $tags->random(rand(0, 3))->pluck('id')->toArray();
            $contact->tags()->sync($randomTags);
        });
    }
}
