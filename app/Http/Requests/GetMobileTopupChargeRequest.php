<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMobileTopupChargeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'mobile_code' => 'required',
            'mobile_number' => 'required|min:6|max:15',
            'country_code' => 'required',
            'amount' => 'required',
        ];
    }
}
