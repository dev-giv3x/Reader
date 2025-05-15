<?php
namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class SearchBookRequest extends ApiRequest
{

    public function rules()
    {
        return [
            'query' => 'required|string|min:2',
        ];
    }
}
