<?php

use Illuminate\Support\Facades\Route;
// ⬇️ この1行でコントローラーの場所をシステムに100%正確に伝えます
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// トップページ（お問い合わせ入力画面 PG01）の表示
Route::get('/', [ContactController::class, 'index']);

// お問い合わせ確認画面（PG02）への送信処理
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
// お問い合わせデータの保存処理と完了画面の表示
Route::post('/contacts', [ContactController::class, 'store']);
// 管理者用お問い合わせ一覧画面（PG04）のルート設定
Route::get('/admin', [ContactController::class, 'admin']);
// CSVエクスポート処理のルート設定
Route::get('/contacts/export', [ContactController::class, 'export']);
