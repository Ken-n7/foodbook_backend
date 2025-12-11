<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'media_url' => 'required|string', // If uploading file, handle in controller; this could be a URL or path
            'media_type' => 'required|string|in:image,video',
            'caption' => 'nullable|string|max:1000',
            'restaurant_id' => 'nullable|exists:restaurants,id',
        ];
    }
}
