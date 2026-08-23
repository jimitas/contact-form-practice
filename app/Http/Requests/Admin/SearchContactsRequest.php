<?php

namespace App\Http\Requests\Admin;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 管理ページの問い合わせ一覧における検索条件を検証するリクエスト。
 */
class SearchContactsRequest extends FormRequest
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
            'name' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'status' => ['nullable', 'array'],
            'status.*' => [Rule::in(array_keys(Contact::statuses()))],
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
            'name' => '氏名',
            'date_from' => '開始日',
            'date_to' => '終了日',
            'status' => 'ステータス',
            'status.*' => 'ステータス',
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
            'string' => ':attributeは文字列で入力してください。',
            'max' => ':attributeは:max文字以内で入力してください。',
            'date' => ':attributeの形式が正しくありません。',
            'after_or_equal' => ':attributeは:dateより後の日付を指定してください。',
            'array' => ':attributeの指定が不正です。',
            'in' => ':attributeの値が不正です。',
        ];
    }
}
