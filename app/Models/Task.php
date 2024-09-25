<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_plan_id',
        'task_name',
        'percentage',
        'description',
        'time_id',
        'photo',

    ];

    /**
     * Get the daily plan that owns the task.
     */
    public function dailyPlan()
    {
        return $this->belongsTo(DailyPlan::class);
    }
    public function time()
    {
        return $this->belongsTo(Time::class);
    }

}
