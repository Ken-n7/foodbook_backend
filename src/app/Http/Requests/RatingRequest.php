<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'restaurant_id' => 'nullable|exists:restaurants,id|required_without:post_id',
            'post_id' => 'nullable|exists:posts,id|required_without:restaurant_id',
        ];
    }
}
