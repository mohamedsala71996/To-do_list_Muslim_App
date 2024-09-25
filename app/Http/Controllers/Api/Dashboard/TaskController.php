<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreDailyTaskRequest;
use App\Http\Requests\Dashboard\UpdateDailyTaskRequest;
use App\Http\Resources\DailyPlanResource;
use App\Models\DailyPlan;
use App\Models\GeneralPercentage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $dailyPlans = DailyPlan::with('tasks')->get();
        return response()->json(
            DailyPlanResource::collection($dailyPlans->load('tasks.time'))  ,
             200);
    }

    public function store(StoreDailyTaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $photoPaths = [];
            if ($request->has('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $photoPaths[] = $photo->store('daily-plan-photos', 'public');
                }
            }

            $dailyPlan = DailyPlan::create([
                'name' => $request->name,
                'photos' => json_encode($photoPaths), // Store the paths as a JSON array
            ]);

            if ($request->has('tasks')) {
                foreach ($request->tasks as $taskData) {
                    $taskPhoto = isset($taskData['photo']) ? $taskData['photo']->store('task-photos', 'public') : null;
                    $dailyPlan->tasks()->create([
                        'task_name' => $taskData['task_name'],
                        'description' => $taskData['description'] ?? null,
                        'time_id' => $taskData['time_id'] ?? null,
                        'percentage' => $taskData['percentage'],
                        'photo' => $taskPhoto,
                    ]);
                }
            }

            DB::commit();
            return new DailyPlanResource($dailyPlan->load('tasks.time'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(UpdateDailyTaskRequest $request, $id)
    {
        $dailyPlan = DailyPlan::findOrFail($id);
        $data = $request->validated();
        // Update the DailyPlan details
        $dailyPlan->update($request->only(['name']));

        // Handle photo updates for DailyPlan
        if ($request->has('photos')) {
            // Delete old photos
            $oldPhotos = json_decode($dailyPlan->photos, true);
            foreach ($oldPhotos as $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }

            // Store new photos
            $photoPaths = [];
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('daily-plan', 'public');
            }
            $dailyPlan->update(['photos' => json_encode($photoPaths)]);
        }
        // Handle task updates and creations
        if ($request->has('tasks')) {
            foreach ($request->tasks as $taskData) {
                if (isset($taskData['id'])) {
                    // Update existing task
                    $task = $dailyPlan->tasks()->findOrFail($taskData['id']);

                    // Handle photo update for task
                    if ($request->hasFile('tasks.*.photo')) {
                        $oldPhoto = $task->photo;
                        $newPhoto = $taskData['photo']->store('task-photos', 'public');

                        // Delete the old photo
                        if ($oldPhoto) {
                            Storage::disk('public')->delete($oldPhoto);
                        }

                        $taskData['photo'] = $newPhoto;
                    }
                    $task->update($taskData);
                } else {
                    // Create new task
                    $taskData['daily_plan_id'] = $dailyPlan->id;

                    // Handle photo upload for new task
                    if ($request->hasFile('tasks.*.photo')) {
                        $taskData['photo'] = $taskData['photo']->store('task-photos', 'public');
                    }

                    $task = $dailyPlan->tasks()->create($taskData);
                }
            }
        }
        // Handle removal of tasks
        if (isset($data['remove_tasks']) && is_array($data['remove_tasks'])) {
            foreach ($data['remove_tasks'] as $taskId) {
                $task = $dailyPlan->tasks()->find($taskId);
                if ($task) {
                    // Delete the task's photo if exists
                    if ($task->photo) {
                        Storage::disk('public')->delete($task->photo);
                    }
                    $task->delete();
                }
            }
        }
        // Return the updated DailyPlan with associated tasks
        return response()->json([
            'status' => 'success',
            'message' => 'Daily Plan and tasks updated successfully',
            'data' =>new DailyPlanResource($dailyPlan->load('tasks.time')),
        ], 200);
    }


    public function destroy($daily_plan_id)
    {
        DB::beginTransaction();
        try {
            // Find the Daily Plan
            $dailyPlan = DailyPlan::find($daily_plan_id);

            if (!$dailyPlan) {
                return response()->json(['error' => 'Daily Plan not found'], 404);
            }

            // Delete Daily Plan Photos
            if (!empty($dailyPlan->photos)) {
                $photos = json_decode($dailyPlan->photos, true); // Decode the JSON field

                foreach ($photos as $photo) {
                    if (Storage::disk('public')->exists($photo)) {
                        Storage::disk('public')->delete($photo); // Delete each photo
                    }
                }
            }

            // Delete Associated Task Photos
            $tasks = $dailyPlan->tasks;
            foreach ($tasks as $task) {
                if ($task->photo ) {
                    Storage::disk('public')->delete($task->photo); // Delete task photo
                }
            }

            // Delete all associated tasks
            $dailyPlan->tasks()->delete();

            // Delete the Daily Plan
            $dailyPlan->delete();

            DB::commit();
            return response()->json(['message' => 'Daily Plan and associated tasks deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
