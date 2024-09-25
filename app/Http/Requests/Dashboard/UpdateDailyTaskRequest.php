<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Set to true or implement authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:daily_plans,name,' . $this->id, // Ignore the current daily plan ID during update

            // Photo validation
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20000', // Validate each photo file

            // Tasks validation
            'tasks' => 'nullable|array',
            'tasks.*.id' => 'nullable|exists:tasks,id', // Check if task exists for updating
            'tasks.*.task_name' => 'required|string|max:255',
            'tasks.*.percentage' => 'required|numeric|min:0|max:100',
            'tasks.*.description' => 'nullable|string', // Include description if applicable
            'tasks.*.photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20000', // Validate task photo
            'tasks.*.time_id' => 'nullable|exists:times,id', // Validate related time_id

            // Remove tasks validation
            'remove_tasks' => 'nullable|array',
            'remove_tasks.*' => 'nullable|integer|exists:tasks,id', // Ensure the tasks to be removed exist
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
            'tasks.*.photo' => 'task photo',
            'tasks.*.time_id' => 'task time',
            'remove_tasks.*' => 'task to be removed',
        ];
    }
}
