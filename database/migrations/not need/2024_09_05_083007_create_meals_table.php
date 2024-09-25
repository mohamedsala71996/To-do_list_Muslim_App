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
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained('daily_plans')->onDelete('cascade');
            $table->string('meal_type');
            $table->string('meal_description');
            // $table->boolean('completed')->default(false);
            $table->decimal('percentage')->default(1);
            $table->string('photo')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
