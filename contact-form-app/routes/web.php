<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── 一般公開画面（PG01〜PG03） ───
Route::get('/', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('/contacts', [ContactController::class, 'store'])->name('contact.store');

// ─── 管理者認証・管理画面グループ (Fortify認証必須ガード) ───
Route::middleware(['auth'])->group(function () {

    // prefix('admin')とname('admin.')を持つグループで管理画面ルートを定義
    Route::prefix('admin')->name('admin.')->group(function () {
        // お問い合わせ一覧管理画面 (PG04) -> URL: /admin
        Route::get('/', [ContactController::class, 'admin'])->name('index');

        // お問い合わせ詳細表示用 -> URL: /admin/contacts/{contact} (GET)
        Route::get('/contacts/{contact}', [ContactController::class, 'show'])->name('contacts.show');

        // 💡新規追加：お問い合わせデータ削除用 -> URL: /admin/contacts/{contact} (DELETE)
        // 詳細画面内の form(method="POST" + @method('DELETE')) の送信先URLに完全適合します
        Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

        // ─── タグCRUD処理 ───
        // タグ追加 -> URL: /admin/tags
        Route::post('/tags', [ContactController::class, 'storeTag'])->name('tags.store');
        // タグ編集画面表示 -> URL: /admin/tags/{tag}/edit
        Route::get('/tags/{tag}/edit', [ContactController::class, 'editTag'])->name('tags.edit');
        // タグ更新 -> URL: /admin/tags/{tag}
        Route::put('/tags/{tag}', [ContactController::class, 'updateTag'])->name('tags.update');
        // タグ削除 -> URL: /admin/tags/{tag}
        Route::delete('/tags/{tag}', [ContactController::class, 'destroyTag'])->name('tags.destroy');
    });

    // ─── エクスポート専用ルート ───
    // 💡 オリジナルBladeの「href="/contacts/export?...」というURL形式に完全適合させます。
    // 💡 認証必須ガード（auth）の内部にあるため、セキュリティ面も200%安全が保証されています。
    Route::get('/contacts/export', [ContactController::class, 'export'])->name('admin.export');
});
