<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTaskPercentage extends Model
{
    use HasFactory;

    protected $table = 'user_task_percentage';

    protected $fillable = [
        'user_id',
        // 'daily_plan_id',
        'date',
        'total_percentage',
    ];

    /**
     * Define relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define relationship with the DailyPlan model.
     */

}
