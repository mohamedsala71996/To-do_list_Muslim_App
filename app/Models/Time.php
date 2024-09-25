<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
    ];

    /**
     * Get the tasks associated with the time.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
