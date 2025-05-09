<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class StoreCommentRequest extends ApiRequest
{

    public function rules()
    {
        return [
            'content' => 'required|string|max:500',
        ];
    }
}