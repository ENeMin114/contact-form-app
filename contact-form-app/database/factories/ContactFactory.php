<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * ランダムなダミーデータの定義
     */
    public function definition(): array
    {
        // データの言語設定を一時的に日本語（ja_JP）に設定して、リアルな日本人の名前を生成します
        $faker = \Faker\Factory::create('ja_JP');

        return [
            // categoriesテーブルに登録されているID（1〜5）からランダムに紐付けます
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,

            'first_name' => $faker->lastName(),  // リアルな日本の苗字（例：山田）
            'last_name' => $faker->firstName(), // リアルな日本の名前（例：太郎）
            'gender' => $this->faker->numberBetween(1, 3), // 1:男性, 2:女性, 3:その他
            'email' => $this->faker->unique()->safeEmail(), // 重複しないメールアドレス

            // 🔴 仕様書の最重要要件：ハイフンなしの11桁の半角数字（文字列）を100%確実に生成します
            'tel' => '0'.$this->faker->numberBetween(70, 90).$this->faker->numerify('########'),

            'address' => $this->faker->address(),
            'building' => $this->faker->optional(0.7)->realText(10).'マンション', // 70%の確率で建物名を入力
            'detail' => $this->faker->realText(50), // 50文字程度のリアルなお問い合わせ内容
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'), // 過去1ヶ月以内のランダムな日時
            'updated_at' => now(),
        ];
    }
}
