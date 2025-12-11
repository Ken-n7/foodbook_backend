<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $this->user,
            'email' => 'required|email|max:255|unique:users,email,' . $this->user,
            'password' => $this->isMethod('post') ? 'required|string|min:8|confirmed' : 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:1000',
        ];
    }
}
