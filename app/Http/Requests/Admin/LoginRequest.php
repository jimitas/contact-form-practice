<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * 管理ページのログイン認証を検証するリクエスト。
 */
class LoginRequest extends FormRequest
{
    /**
     * このリクエストを実行する権限があるかどうかを判定する。
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルールを返す。
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * バリデーションエラーメッセージ用の項目名を返す。
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'メールアドレス',
            'password' => 'パスワード',
        ];
    }

    /**
     * バリデーションエラーメッセージを日本語で返す。
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attributeを入力してください。',
            'email' => ':attributeの形式が正しくありません。',
        ];
    }

    /**
     * 入力されたメールアドレスとパスワードで認証を試みる。
     */
    public function authenticate(): void
    {
        if (! Auth::attempt($this->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ]);
        }
    }
}
