<?php
namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class StoreRatingRequest extends ApiRequest
{

    public function rules()
    {
        return [
            'value' => 'required|integer|min:1|max:5',
        ];
    }
}
