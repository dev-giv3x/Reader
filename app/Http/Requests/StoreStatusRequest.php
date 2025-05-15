<?php
namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;

class StoreStatusRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'status' => 'required|in:reading,completed,planned,dropped,liked',
        ];
    }
}
