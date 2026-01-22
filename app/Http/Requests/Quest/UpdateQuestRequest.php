<?php

namespace App\Http\Requests\Quest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestRequest extends FormRequest
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
            'questId' => 'required',
            'rewardExp' => 'sometimes|numeric|min:1',
            'rewardPoints' => 'sometimes|numeric|min:1',
            'questTasks' => 'sometimes|array',
            'questTasks.*.title' => 'required_with:questTasks',
            'questTasks.*.description' => 'required_with:questTasks',
            'questTasks.*.rewardExp' => 'required_with:questTasks',
            'questTasks.*.rewardPoints' => 'required_with:questTasks',
            'questTasks.*.order' => 'required_with:questTasks'
        ];
    }
}
