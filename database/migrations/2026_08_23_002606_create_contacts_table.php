<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * マイグレーションを実行する。
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 名前
            $table->string('email'); // メールアドレス
            $table->string('subject'); // 件名
            $table->text('body'); // 本文
            $table->string('status')->default('新規'); // ステータス（新規/対応中/解決済み）
            $table->timestamps();
        });
    }

    /**
     * マイグレーションを元に戻す。
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
