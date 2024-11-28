<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' =>'required|string|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'user_name' => 'required|string|unique:users_users|min:4|max:24',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ];
    }
}
