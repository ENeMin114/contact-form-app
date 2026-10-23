<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify; // 🔴 これがシステムと画面を繋ぐ最重要の宣言です

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ユーザー登録時のデータ作成処理を紐付け
        Fortify::createUsersUsing(CreateNewUser::class);

        // 🔴 ログイン画面（auth/login.blade.php）を表示させる設定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 🔴 ユーザー登録画面（auth/register.blade.php）を表示させる設定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // 連続ログイン間違いを制限する防犯機能
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });
    }
}
