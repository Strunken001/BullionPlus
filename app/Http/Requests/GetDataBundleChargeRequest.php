<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetDataBundleChargeRequest extends FormRequest
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
            'operator_id'    => 'required',
            'amount'    => 'required',
            'mobile_number'    => 'required',
            'iso2'    => 'required',
            'variation_code' => 'required|string',
        ];
    }
}
