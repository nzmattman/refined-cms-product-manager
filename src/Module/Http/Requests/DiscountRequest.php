<?php

namespace RefinedDigital\ProductManager\Module\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
{
    /**
     * Determine if the service is authorized to make this request.
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
     * @return array
     */
    public function rules()
    {

        $args = [
            'name'           => ['required' => 'required'],
            'price'          => ['required' => 'required_without:percent'],
            'percent'        => ['required' => 'required_without:price'],
            'code'           => ['required' => 'required_if:type_id,1'],
            'user_group_id'  => ['required' => 'required_if:type_id,2', 'not0' => 'not0'],
        ];

        // return the results to set for validation
        return $args;
    }

    public function messages()
    {
        return [
            'user_group_id.not0' => 'The user group can not be empty',
            'code.required_if' => 'The code Field is required',
        ];
    }
}
