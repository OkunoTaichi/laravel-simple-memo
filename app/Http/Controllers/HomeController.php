<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;
use App\Models\MemoTag;
use DB;

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

    //  ホーム画面に返す
    public function index()
    {       
        $tags = Tag::where('user_id', '=', \Auth::id()) 
        -> whereNull('deleted_at')
        -> orderBy('id', 'DESC')
        ->get();
        // dd($tags);

        return view('create' , compact('tags'));
    }

    public function store(Request $request)
    {
        // $requestの中身を全て$postsに格納
        $posts = $request->all();
        // dump die の略 -> メソッドの引数の取った値を展開して止める -> データを確認するためのデバッグメソッド
        // dd($posts);

        // =======ここからトランザクション開始======
        DB::transaction(function()use($posts){
            //メモIDをインサートして取得
            $memo_id = Memo::insertGetId(['title' => $posts['title'],'content' => $posts['content'],'user_id' => \Auth::id()]);

            //新規タグが既にtagsテーブルに存在するかチェック
            $tag_exists = Tag::where('user_id', '=' ,\Auth::id())-> where('name', '=', $posts['new_tag'])
            ->exists();

            //新規タグが入力されているかチェック
            if(!empty(($posts['new_tag']) || $posts['new_tag'] === "0") && !$tag_exists){
                //新規タグが存在しなければ、tagsテーブルにインサート->IDを取得(GetId)
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                //memo_tagsnにインサートして、メモとタグを紐付ける
                MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag_id]);
            }

            //既存のタグが紐付けられた場合->memo_tagsにインサートする
            if(!empty($posts['tags'][0])){
                foreach($posts['tags'] as $tag){
                    MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
                }
            }
        });

        // =======ここまでがトランザクションの範囲======


        

        return redirect( route('home') );
    }

    public function edit($id)
    {
        $edit_memo = Memo::select('memos.*', 'tags.id AS tag_id')
            ->leftJoin('memo_tags', 'memo_tags.memo_id', '=', 'memos.id')
            ->leftJoin('tags', 'memo_tags.tag_id', '=', 'tags.id')
            ->where('memos.user_id', '=' , \Auth::id())
            ->where('memos.id', '=' , $id)
            ->whereNull('memos.deleted_at')
            ->get();
        
        $include_tags =[];

        foreach($edit_memo as $memo){
            array_push($include_tags, $memo['tag_id']);
        }

        $tags = Tag::where('user_id', '=', \Auth::id()) -> whereNull('deleted_at') -> orderBy('id', 'DESC')
        ->get();

        // return view('edit/{id}' , compact('edit_memo', 'include_tags' ,'tags'));
        return view('edit' , compact('edit_memo', 'include_tags' ,'tags'));
    }



   



    public function update(Request $request)
    {
        $posts = $request->all();
        // dd($posts);

        //トランザクションスタート
        DB::transaction(function() use($posts){
            Memo::where('id', $posts['memo_id'])->update(['title' => $posts['title']],['content' => $posts['content']]);
            
            //一旦メモとタグの紐付けを削除
            MemoTag::where('memo_id', '=', $posts['memo_id'])->delete();
            //再度メモとタグの紐付け
            if(!empty($posts['tags'])){
                foreach($posts['tags'] as $tag){
                    MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag]);
                }
            }



            //新規タグが既にtagsテーブルに存在するかチェック
            //もし新しいタグの入力があれば、インサートして紐付ける
            $tag_exists = Tag::where('user_id', '=' ,\Auth::id())-> where('name', '=', $posts['new_tag'])
            ->exists();
    
            //新規タグが入力されているかチェック
            if(!empty(($posts['new_tag']) || $posts['new_tag'] === "0") && !$tag_exists){
                //新規タグが存在しなければ、tagsテーブルにインサート->IDを取得(GetId)
                $tag_id = Tag::insertGetId(['user_id' => \Auth::id(), 'name' => $posts['new_tag']]);
                //memo_tagsnにインサートして、メモとタグを紐付ける
                MemoTag::insert(['memo_id' => $posts['memo_id'], 'tag_id' => $tag_id]);
            }
        });
        
        //トランザクションここまで
        return redirect( route('home') );
    }

    public function destory(Request $request)
    {
        $posts = $request->all();
        // dd($posts);

        // Memo::where('id', $posts['memo_id'])->delete();<-これは物理削除になるのでデータベースからも抹消されてしまう。
        Memo::where('id', $posts['memo_id'])->update(['deleted_at' => date("Y-m-d H:i:s", time())]);
        
        return redirect( route('home') );
    }
}
