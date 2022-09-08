<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreSaleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        switch ($this->method()) {
            case Request::METHOD_POST:
            case Request::METHOD_PUT:
                $rules = [
                    "price"       => "required|numeric|min:0",
                    "currency"    => "required|in:" . implode(",", array_values(config("seller_data.currency"))),
                    "productName" => "required"
                ];
                break;
        }

        return $rules;
    }
}
