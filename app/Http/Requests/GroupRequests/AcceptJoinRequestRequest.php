<?php

namespace App\Http\Requests\GroupRequests;

use Illuminate\Foundation\Http\FormRequest;

class AcceptJoinRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
    return [
        'user_name' => 'required|exists:users_users,user_name',
        'group_id' => 'required|exists:groups,id',
     ];
    }
}

