<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

/**
 * Googleアカウントによる管理ページへのログインを扱うコントローラー。
 */
class GoogleAuthController extends Controller
{
    /**
     * Googleの認可画面へリダイレクトする。
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Googleからのコールバックを処理し、登録済みの管理者としてログインする。
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Googleログインに失敗しました。もう一度お試しください。']);
        }

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            return redirect()->route('admin.login')
                ->withErrors(['email' => 'このGoogleアカウントは管理者として登録されていません。']);
        }

        Auth::login($user);

        request()->session()->regenerate();

        return redirect()->intended(route('admin.contacts.index'));
    }
}
