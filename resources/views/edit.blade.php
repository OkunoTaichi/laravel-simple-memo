@extends('layouts.app')

@section('content')
<div class="card" style="overflow:hidden;">
    <div class="card-header d-flex justify-content-between">
        メモ編集
        <form action="{{ route('destory') }}" method="POST">
            @csrf
            <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
            <button type="submit">削除</button>
        </form>
    </div>
    <!-- route('store')と書くと-> /store -->
    <form class="card-body card_height" action="{{ route('update') }}" method="POST">
        @csrf
        <input type="hidden" name="memo_id" value="{{ $edit_memo[0]['id'] }}">
        <div class="form-group mb-3 d-flex">
            <textarea class="form-control text-title-height" name="title" placeholder="ここにメモを入力">{{$edit_memo[0]['title']}}</textarea> 
        </div>
        <div class="form-group">
            <textarea class="form-control t-height" name="content" rows="3" placeholder="ここにメモを入力">{{$edit_memo[0]['content']}}</textarea>
        </div>
        @foreach($tags as $t)
        <div class="form-check form-check-inline mt-3 mb-3">
            <!-- {{-- 3項演算子->if文を1行で書く方法 {{ 条件 ？ trueだったら : falseだったら}} --}} -->
            <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $t['id']}}" id="{{ $t['id']}}" {{ in_array($t['id'], $include_tags) ? 'checked' : '' }} >
            <label class="form-check-label" for="{{ $t['id']}}">
                {{ $t['name']}}
            </label>
        </div>
        @endforeach

        <input type="text" class="form-control w-50 mb-3 mt-3" name="new_tag" placeholder="新しいタグを入力">
        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection


