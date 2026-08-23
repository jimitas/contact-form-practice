<?php

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 管理画面でのお問い合わせステータス更新を検証するリクエスト。
 */
class UpdateContactStatusRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Contact::statuses()))],
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
            'status' => 'ステータス',
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
            'required' => ':attributeを選択してください。',
            'in' => ':attributeの値が不正です。',
        ];
    }
}
