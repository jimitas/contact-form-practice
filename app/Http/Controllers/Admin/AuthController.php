<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * 管理ページのログイン・ログアウトを扱うコントローラー。
 */
class AuthController extends Controller
{
    /**
     * ログイン画面を表示する。
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }

    /**
     * 認証を行い、ログインする。
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // セッション固定攻撃を防ぐため、認証成功時にセッションIDを再生成する
        $request->session()->regenerate();

        return redirect()->intended(route('admin.contacts.index'));
    }

    /**
     * ログアウトする。
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'ログアウトしました。');
    }
}
