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
        Schema::create('contact_tag', function (Blueprint $table) {
            $table->id();
            // 🔴 外部キー制約：contactsテーブルが消えたら自動削除（ON DELETE CASCADE）
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            // 🔴 外部キー制約：tagsテーブルが消えたら自動削除（ON DELETE CASCADE）
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_tag');
    }
};
