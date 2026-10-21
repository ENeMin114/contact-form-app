<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tagモデルの一括登録を許可するために、まず中身を設定します
        // テスト・評価用に5つのタグを定義します
        $tags = [
            '女性向け',
            '男性向け',
            '若年層向け',
            'シニア向け',
            'その他',
        ];

        foreach ($tags as $tag) {
            Tag::create(['name' => $tag]);
        }
    }
}
