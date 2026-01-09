<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-._,!?()\'"":;@#$%&*+=\/]+$/',
            ],
            'description' => [
                'nullable',
                'string',
                'regex:/^[a-zA-Z0-9\s\-._,!?()\'"":;@#$%&*+=\/]+$/',
            ],
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.regex' => 'Title contains invalid characters.',
            'description.regex' => 'Description contains invalid characters.',
        ];
    }
}
