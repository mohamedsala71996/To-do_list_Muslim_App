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
        Schema::create('personal_care', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_plan_id')->constrained('daily_plans')->onDelete('cascade');
            $table->string('care_time')->nullable();
            $table->string('care_activity');
            // $table->boolean('completed')->default(false);
            $table->decimal('percentage')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_care');
    }
};
