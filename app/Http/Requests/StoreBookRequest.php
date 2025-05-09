<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class StoreBookRequest extends ApiRequest
{

    public function rules()
    {
        return [
            'title' => 'required|string|max:75',
            'author' => 'required|string|max:50',
            'description' => 'required|string',
            'is_public' => 'required|boolean',
            'file' => 'required|file|mimes:pdf,epub|max:10000',
        ];
    }
}