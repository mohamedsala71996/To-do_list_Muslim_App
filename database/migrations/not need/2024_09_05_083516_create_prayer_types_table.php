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
        Schema::create('prayer_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prayer_time_id')->constrained('prayer_times')->onDelete('cascade');
            $table->string('prayer_type');
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
        Schema::dropIfExists('prayer_types');
    }
};
