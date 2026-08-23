<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * お問い合わせへの返信内容を検証するリクエスト。
 */
class StoreContactReplyRequest extends FormRequest
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
            'body' => '返信本文',
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
            'string' => ':attributeは文字列で入力してください。',
            'max' => ':attributeは:max文字以内で入力してください。',
        ];
    }
}
