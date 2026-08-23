<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * お問い合わせフォームの入力内容を検証するリクエスト。
 */
class ContactRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:2000',
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
            'name' => 'お名前',
            'email' => 'メールアドレス',
            'subject' => '件名',
            'body' => '本文',
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
            'string' => ':attributeは文字列で入力してください。',
            'max' => ':attributeは:max文字以内で入力してください。',
        ];
    }
}
