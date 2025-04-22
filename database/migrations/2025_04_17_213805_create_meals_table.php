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

            // Foreign Keys
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            // Meal Info
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('cheif_id')->constrained()->onDelete('cascade');
            // Ratings & Availability
            $table->decimal('rate', 3, 2)->nullable()->comment('Customer rating from 0.00 to 5.00');
            $table->unsignedInteger('delivery_time')->default(0)->comment('Delivery time in minutes');
            $table->boolean('is_available')->default(true);

            // Constraints & Indexes
            $table->unique(['restaurant_id', 'name']);

            // Timestamps
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
