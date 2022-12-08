<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        // $requestの中身を全て$postsに格納
        $posts = $request->all();
        dump die の略 -> メソッドの引数の取った値を展開して止める -> データを確認するためのデバッグメソッド
        dd('$posts');
        return view('create');
    }
}
