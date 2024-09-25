<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPlan extends Model
{
    use HasFactory;
    protected $table='daily_plans';
    protected $fillable = [
        'name',
        'photos',
        'user_id',
    ];

    protected $casts = [
        'photos' => 'array', // Ensure that photos are treated as an array
    ];

    /**
     * Get the user that owns the daily plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tasks for the daily plan.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('time_id');
    }
}
