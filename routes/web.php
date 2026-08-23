<?php

use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// トップページはお問い合わせフォームへリダイレクトする
Route::redirect('/', '/contact');

// お問い合わせフォーム（入力・確認・送信・完了）
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');

// 管理ページ（お問い合わせの一覧・詳細・ステータス管理）
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::patch('contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.updateStatus');
});
