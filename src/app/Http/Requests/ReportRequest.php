<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReportRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check() && Auth::user()->is_admin;
    }

    public function rules()
    {
        return [
            'post_id' => 'required|exists:posts,id',
            'reason' => 'required|string|max:500',
        ];
    }
}
