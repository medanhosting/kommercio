<?php

namespace Kommercio\Http\Requests\Backend\Order;

use Kommercio\Http\Requests\Request;

class OrderFormRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        $rules = [
            'store_id' => 'required|integer',
        ];

        return $rules;
    }
}