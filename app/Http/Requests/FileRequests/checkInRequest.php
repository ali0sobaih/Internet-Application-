<?php

namespace App\Http\Requests\FileRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class checkInRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'files' => 'required|array',
            'files.*.file_id' => 'required|integer|min:1',
            'files.*.version' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'files.required' => 'A file list is required.',
            'files.array' => 'The :attributes must be an array.',
            'files.*.file_id.required' => 'File ID is required for each item in the list.',
            'files.*.file_id.integer' => 'File ID must be an integer.',
            'files.*.file_id.min' => 'File ID should be at least 1.',
            'files.*.version.required' => 'Version number is required for each item in the list.',
            'files.*.version.integer' => 'Version number must be an integer.',
            'files.*.version.min' => 'Version number should be at least 1.',
            'files.*.version.max' => 'Version number should not exceed 1000.',
        ];
    }
}
