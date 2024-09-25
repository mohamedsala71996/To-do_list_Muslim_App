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
        Schema::create('media_photos', function (Blueprint $table) {
            $table->id();
            $table->string('header_photo')->nullable(); // Stores the header photo
            $table->string('percentage_photo')->nullable(); // Stores the percentage photo
            $table->string('goals_photo')->nullable(); // Stores the goals photo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_photos');
    }
};
