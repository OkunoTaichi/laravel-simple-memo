<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->longText('title');
            $table->longText('content');
            $table->unsignedBigInteger('user_id');
            // 論理削除を定義->deleted_atを自動生成
            $table->softDeletes();
            // timestampと書いてしまうと、レコード挿入時に更新時に値が入らないのでDB::rawで直接書いています
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('memos', function (Blueprint $table) {
            $table->longText('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('memos');
    }
}
