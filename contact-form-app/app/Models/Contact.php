<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // テーブルに安全に一括登録できるようにカラムの許可を設定します
    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    /**
     * 🔴 1対多の要件：categoriesテーブルとの関係性を定義します（所属関係）
     * これにより、一覧画面で「商品のお届けについて」などのカテゴリ名が綺麗に引っ張ってこれるようになります！
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 多対多の要件：tagsテーブルとの関係性を定義します（中間テーブル contact_tag）
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
