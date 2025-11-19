<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditAccountInfoRequest extends FormRequest
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
            'username' => 'sometimes|string|unique:users,username',
            'firstName' => 'sometimes|string',
            'lastName' => 'sometimes|string',
            'birthdate' => 'sometimes|date',
            'gender' => ['sometimes|', Rule::in(['M', 'F', 'Other', 'Prefer not to say'])],
            'city' => 'sometimes|string',
            'province' => 'sometimes|string',
            'country' => 'sometimes|string',
            'contactNumber' => 'sometimes|string|regex:/^09\d{9}$/',
            'bio' => 'sometimes|string',
        ];
    }
}
