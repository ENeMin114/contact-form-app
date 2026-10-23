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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            // 🔴 外部キー制約：categoriesテーブルと紐付け、カテゴリが消えたら連動して消える設定（ON DELETE CASCADE）
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            $table->string('first_name');
            $table->string('last_name');
            $table->tinyInteger('gender'); // 仕様書の整数型（1:男性, 2:女性, 3:その他）
            $table->string('email');

            // 🔴 最重要注意点：電話番号は int型 ではなく、必ずハイフンなしの文字列型 varchar(11) に設定します
            $table->string('tel', 11);

            $table->string('address');
            $table->string('building')->nullable(); // 建物名は空欄（NULL）を許可
            $table->text('detail');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
