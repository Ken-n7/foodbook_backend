<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class FriendRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'friend_id' => 'required|exists:users,id|not_in:' . Auth::id(),
            'status' => 'nullable|in:pending,accepted',
        ];
    }
}
