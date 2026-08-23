<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\ContactReplyController as AdminContactReplyController;
use App\Http\Controllers\Admin\GoogleAuthController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

// トップページはお問い合わせフォームへリダイレクトする
Route::redirect('/', '/contact');

// お問い合わせフォーム（入力・確認・送信・完了）
Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/thanks', [ContactController::class, 'thanks'])->name('contact.thanks');

// 管理ページ（お問い合わせの一覧・詳細・ステータス管理・返信、およびログイン・ログアウト）
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'create'])->name('login');
        Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
        Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
        Route::patch('contacts/{contact}/status', [AdminContactController::class, 'updateStatus'])->name('contacts.updateStatus');
        Route::post('contacts/{contact}/replies', [AdminContactReplyController::class, 'store'])->name('contacts.replies.store');
    });
});
