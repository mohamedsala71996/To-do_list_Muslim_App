<?php

namespace App\Http\Resources;

use App\Models\Task;
use App\Models\Time;
use Illuminate\Http\Resources\Json\JsonResource;

class DailyPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return [
        //     'id' => $this->id,
        //     'name' => $this->name,
        //     'photos' => $this->photos ? json_decode($this->photos, true) : [],
        //     'tasks' => TaskResource::collection($this->whenLoaded('tasks')->where('time_id',null)),
        //     'created_at' => $this->created_at,
        //     'updated_at' => $this->updated_at,
        // ];

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'photos' => $this->photos ? json_decode($this->photos, true) : [],
            'checklists' => TaskResource::collection($this->whenLoaded('tasks')->where('time_id',null)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
    ];

    if ($this->name === 'الصلوات والفرائض') {
        $times = Time::whereNotIn('time', ['صباحاً', 'مساءً'])->with('tasks')->get();
        $data['checklists'] = TimeResource::collection($times);
    }

    if ($this->name === 'العناية اليومية') {
        $timesArray = [
            'الفجر', 'الضحى', 'الظهر', 'العصر', 'المغرب', 'العشاء', 'الوتر', 'قيام الليل'
        ];
        $times = Time::whereNotIn('time', $timesArray)->with('tasks')->get();
        $tasksNotHaveTime=Task::where('daily_plan_id', $this->id)->where('time_id',null)->get();
        $mergedData = [
            TimeResource::collection($times),
            CustomTaskResource::collection($tasksNotHaveTime),
        ];
        $data['checklists'] = $mergedData;
    }

        return $data;
    }

    }

