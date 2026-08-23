<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * お問い合わせフォーム（入力・確認・送信・完了）を扱うコントローラー。
 */
class ContactController extends Controller
{
    /**
     * 入力フォームを表示する。
     */
    public function create(): View
    {
        // 確認画面から「戻る」で遷移してきた場合は、保存済みの入力内容を復元する
        $input = session('contact.input', []);

        return view('contact.create', ['input' => $input]);
    }

    /**
     * 入力内容を検証し、確認画面を表示する。
     */
    public function confirm(ContactRequest $request): View
    {
        $validated = $request->validated();

        // 送信時にそのまま使えるよう、検証済みの入力内容をセッションに保持する
        session(['contact.input' => $validated]);

        return view('contact.confirm', ['input' => $validated]);
    }

    /**
     * セッションに保持した入力内容を保存する。
     */
    public function store(): RedirectResponse
    {
        $input = session('contact.input');

        if (! $input) {
            return redirect()
                ->route('contact.create')
                ->withErrors(['form' => '入力内容が見つかりませんでした。もう一度入力してください。']);
        }

        Contact::create([
            ...$input,
            'status' => Contact::STATUS_NEW,
        ]);

        session()->forget('contact.input');

        return redirect()->route('contact.thanks');
    }

    /**
     * 送信完了画面を表示する。
     */
    public function thanks(): View
    {
        return view('contact.thanks');
    }
}
