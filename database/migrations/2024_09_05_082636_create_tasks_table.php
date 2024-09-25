<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained('daily_plans')->onDelete('cascade');
            $table->string('task_name');
            $table->string('description')->nullable();
            $table->foreignId('time_id')->nullable()->constrained('times')->onDelete('set null');
            // $table->string('time')->nullable();
            // $table->boolean('completed')->default(false);
            $table->string('photo')->nullable(); // assuming photos as an array of images
            $table->string('percentage')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
