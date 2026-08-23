<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * 管理ページのログイン画面表示・ログアウトを扱うコントローラー。
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
