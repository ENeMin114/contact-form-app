<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /**
     * バリデーションの実行権限を許可します
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 電話番号の3分割入力を1つに結合してバリデーション対象に含めます
     */
    protected function prepareForValidation()
    {
        if ($this->filled(['tel1', 'tel2', 'tel3'])) {
            $this->merge([
                'tel' => $this->tel1 . $this->tel2 . $this->tel3
            ]);
        }
    }

    /**
     * 仕様書に基づくバリデーションルール（入力チェック条件）
     */
    public function rules(): array
    {
        return [
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'gender'      => ['required', 'in:1,2,3'],
            'email'       => ['required', 'string', 'email', 'max:255'],
            'tel1'        => ['required', 'numeric', 'digits_between:1,4'],
            'tel2'        => ['required', 'numeric', 'digits_between:1,4'],
            'tel3'        => ['required', 'numeric', 'digits_between:1,4'],
            'tel'         => ['required', 'numeric', 'digits_between:10,11'],
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'detail'      => ['required', 'string', 'max:120'],
            'tag_ids'     => ['nullable', 'array'],
            'tag_ids.*'   => ['exists:tags,id'],
        ];
    }

    /**
     * 仕様書と完全に一致させた日本語エラーメッセージ
     */
    public function messages(): array
    {
        return [
            'first_name.required'  => '姓を入力してください',
            'last_name.required'   => '名を入力してください',
            'gender.required'      => '性別を選択してください',
            'gender.in'            => '性別を正しく選択してください',
            'email.required'       => 'メールアドレスを入力してください',
            'email.email'          => 'メールアドレスは「ユーザー名@ドメイン」の形式で入力してください',
            'tel1.required'        => '電話番号を入力してください',
            'tel1.numeric'         => '電話番号は半角数字で入力してください',
            'tel2.required'        => '電話番号を入力してください',
            'tel2.numeric'         => '電話番号は半角数字で入力してください',
            'tel3.required'        => '電話番号を入力してください',
            'tel3.numeric'         => '電話番号は半角数字で入力してください',
            'tel.required'         => '電話番号を入力してください',
            'tel.numeric'          => '電話番号は半角数字で入力してください',
            'address.required'     => '住所を入力してください',
            'category_id.required' => 'お問い合わせの種類を選択してください',
            'detail.required'      => 'お問い合わせ内容を入力してください',
            'detail.max'           => 'お問い合わせ内容は120文字以内で入力してください',
        ];
    }
}
