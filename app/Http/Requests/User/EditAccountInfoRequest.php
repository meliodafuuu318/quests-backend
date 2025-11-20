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
            'username' => 'string|unique:users,username',
            'firstName' => 'string',
            'lastName' => 'string',
            'birthdate' => 'date',
            'gender' => [Rule::in(['M', 'F', 'Other', 'Prefer not to say'])],
            'city' => 'string',
            'province' => 'string',
            'country' => 'string',
            'contactNumber' => 'string|regex:/^09\d{9}$/',
            'bio' => 'string',
        ];
    }
}
