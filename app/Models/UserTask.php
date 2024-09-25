<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    use HasFactory;
    protected $table = 'user_task';
    protected $fillable = ['user_id', 'daily_plan_id','task_id','completed','percentage'];

    

}
