<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Set to true to allow all users or implement your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:daily_plans,name',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20000', // Validate each daily plan photo

            'tasks' => 'nullable|array',
            'tasks.*.task_name' => 'required|string|max:255',
            'tasks.*.percentage' => 'required|numeric|min:0|max:100',
            'tasks.*.description' => 'nullable|string', // Task description
            'tasks.*.time_id' => 'nullable|exists:times,id', // Reference to the times table
            'tasks.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20000', // Single photo for each task
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'photos.*' => 'photo',
            'tasks.*.task_name' => 'task name',
            'tasks.*.percentage' => 'task percentage',
            'tasks.*.description' => 'task description',
            'tasks.*.time_id' => 'time',
            'tasks.*.photo' => 'task photo',
        ];
    }
}
