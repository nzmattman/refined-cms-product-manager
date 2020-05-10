<?php

namespace RefinedDigital\ProductManager\Module\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VariationRequest extends FormRequest
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
            'name'                => ['required' => 'required'],
            'variations'          => ['required' => 'required'],
        ];

        // return the results to set for validation
        return $args;
    }
}
/*
Beef
Pork & Apple Cider
Lamb & Rosemary
Hot & Spicy
French Toulouse
Venison, Merlot & Cracked Pepper
Italian
Pork
Spanish Chorizo
*/
