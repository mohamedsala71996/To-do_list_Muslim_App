<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\DailyPlanResource;
use App\Http\Resources\EveryDayPercentageResource;
use App\Http\Resources\TimeResource;
use App\Models\DailyPlan;
use App\Models\GeneralPercentage;
use App\Models\Task;
use App\Models\Time;
use App\Models\UserTask;
use App\Models\UserTaskPercentage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // public function index()
    // {
    //     $dailyPlans = DailyPlan::with('tasks')->get();

    //     $times = Time::with('tasks')->get();

    //     return response()->json([
    //         'daily_plans' => DailyPlanResource::collection($dailyPlans->load('tasks.time')),
    //         'times' => $times
    //     ], 200);
    // }
    // public function times()
    // {
    //     $times = Time::with('tasks')->get();
    //     return response()->json(
    //         DailyPlanResource::collection($times->load('tasks.time')),
    //         200
    //     );
    // }

    public function index()
    {
        $dailyPlans = DailyPlan::with('tasks')->get();

        // $times = Time::with('tasks')->get();

        $mergedData = [
            'daily_plans' => DailyPlanResource::collection($dailyPlans),
            // 'الصلوات والفرائض' =>  TimeResource::collection($times),
        ];

        return response()->json($mergedData, 200);
    }

    public function show($daily_plan_id)
    {
        // Find the daily plan by ID
        $dailyPlan = DailyPlan::with('tasks.time')->find($daily_plan_id);

        if (!$dailyPlan) {
            return response()->json(['error' => 'Daily Plan not found'], 404);
        }

        // Return the daily plan resource with its tasks
        return response()->json([
            'message' => 'Daily Plan retrieved successfully',
            'data' => new DailyPlanResource($dailyPlan)
        ], 200);
    }
    public function store(Request $request, $daily_plan_id)
    {
        // Validate the request data
        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.task_id' => 'required|exists:tasks,id',
            'tasks.*.completed' => 'nullable|boolean',
            'tasks.*.note' => 'nullable|string',
        ]);

        // Get the authenticated user
        $user_id = auth()->user()->id;

        $generalPercentage = GeneralPercentage::first();
        $existingTask = UserTask::where('user_id', $user_id)
        // ->where('task_id', $task['task_id'])
        ->where('daily_plan_id', $daily_plan_id)
        ->whereDate('created_at', Carbon::today())
        ->delete();
        // Iterate through the tasks array and update or create the pivot table records
        foreach ($request->tasks as $task) {
            $percentage = Task::find($task['task_id'])->percentage;


            // Try to find the existing task for the user created today

            // $existingTask = UserTask::where('user_id', $user_id)
            //     ->where('task_id', $task['task_id'])
            //     // ->where('daily_plan_id', $daily_plan_id)
            //     ->whereDate('created_at', Carbon::today())
            //     ->first();

            // if ($existingTask) {
            //     // Update the existing task if found
            //     $existingTask->update([
            //         'completed' => $task['completed'],
            //         'note' => $task['note'] ?? null,
            //         'percentage' =>$generalPercentage->percentage?? $percentage,
            //     ]);
            // } else {
                // Create a new task if not found
                UserTask::create([
                    'user_id' => $user_id,
                    'task_id' => $task['task_id'],
                    'completed' => $task['completed'],
                    'daily_plan_id' => $daily_plan_id,
                    'note' => $task['note'] ?? null,
                    'percentage' => $generalPercentage->percentage ?? $percentage,
                ]);
            }

        // }
        $task_count=Task::count();

        $generalPercentage = GeneralPercentage::first();
        if (!$generalPercentage) {
            $totalPercentage = UserTask::where('user_id', $user_id)->where('completed', 1)
            ->whereDate('created_at', Carbon::today()) // Use 'created_at' instead of 'date'
            ->sum('percentage')/$task_count * 100;

        }else {
            $totalPercentage = ((UserTask::where('user_id', $user_id)->where('completed', 1)
            ->whereDate('created_at', Carbon::today()) // Use 'created_at' instead of 'date'
            ->count() * $generalPercentage->percentage) / $task_count)  * 100;

        }

    // Update or create the record in user_task_percentage table
     UserTaskPercentage::updateOrCreate(
        [
            'user_id' => $user_id,
            'date' => Carbon::today()->toDateString(), // Record for today
        ],
        [
            'total_percentage' => $totalPercentage,
        ]
    );

    return response()->json([
        'message' => 'User tasks updated or created successfully',
        // 'total_percentage' => $totalPercentage,
    ], 200);

    }

    // public function getPercentage()
    // {
    //     // Get the authenticated user
    //     $user_id = auth()->user()->id;

    //     // // Get the total percentage for the user created today
    //     // $totalPercentage = UserTaskPercentage::where('user_id', $user_id)
    //     //     ->whereDate('date', Carbon::today())
    //     //     ->first();
    //         $totalPercentage=  UserTask::where('user_id', $user_id)->whereDate('created_at', Carbon::today())->avg('percentage');

    //     if (!$totalPercentage) {
    //         return response()->json(['error' => 'No percentage found for today'], 404);
    //     }



    //     return response()->json([
    //         'total_percentage' => $totalPercentage->total_percentage,
    //     ], 200);

    // }

    public function getPercentage()
{
    // Get the authenticated user
    $user_id = auth()->user()->id;
    $task_count=Task::count();
    $generalPercentage = GeneralPercentage::first();
        if (!$generalPercentage) {
            $totalPercentage = UserTask::where('user_id', $user_id)->where('completed', 1)
            ->whereDate('created_at', Carbon::today()) // Use 'created_at' instead of 'date'
            ->sum('percentage')/$task_count * 100;

        }else {
            $totalPercentage = ((UserTask::where('user_id', $user_id)->where('completed', 1)
            ->whereDate('created_at', Carbon::today()) // Use 'created_at' instead of 'date'
            ->count() * $generalPercentage->percentage) / $task_count)  * 100;

        }

        // if (!$totalPercentage) {
        //     return response()->json(['error' => 'No percentage found for today'], 404);
        // }


    return response()->json([
        'total_percentage' => $totalPercentage,
    ], 200);
}

public function everyDayPercenage()
{
    $user_id = auth()->user()->id;

   $getAll= UserTaskPercentage::where('user_id', $user_id)->get();

   return response()->json([
        'data' => EveryDayPercentageResource::collection($getAll),
    ], 200);
}


}
