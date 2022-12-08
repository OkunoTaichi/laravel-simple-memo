<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Memo;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 全てのメソッドが呼ばれる前より先に呼ばれるメソッド
        view()->composer('*' , function($view){
            $memos = Memo::select('memos.*')
                ->where('user_id', '=' , \Auth::id())//ユーザーIDとログインしているIDが同じもので
                ->whereNull('deleted_at')//デリートの履歴がないもので
                ->orderBy('updated_at', 'DESC')//アップデートされた日時が新しいものから
                ->get();// DBに架空のデータを作成し、上記の項目が正しくできているか確認する。

            $view->with('memos', $memos);
        });
        
    }
}
